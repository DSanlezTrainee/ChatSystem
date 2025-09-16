<template>
    <div class="flex h-screen bg-black">
        <Sidebar
            :rooms="rooms"
            :users="users"
            :selectedRoom="selectedRoom"
            :selectedUser="selectedUser"
            :currentUser="currentUser"
            :jetstream="$page.props.jetstream"
            @select-room="selectRoom"
            @select-dm="selectDM"
            @create-room="openCreateRoomModal"
            @add-users-to-room="openAddUsersToRoom"
        />
        <div class="flex-1 flex flex-col">
            <ChatWindow
                v-if="selectedRoom || selectedUser"
                :messages="messages"
                :title="selectedRoom ? selectedRoom.name : selectedUser.name"
                :currentUser="currentUser"
                @send-message="sendMessage"
            />
            <div
                v-else
                class="flex-1 flex items-center justify-center text-gray-400 text-xl"
            >
                Select a room or direct message to start chatting.
            </div>
        </div>

        <!-- User Selection Modal -->
        <UserSelectionModal
            :show="showUserSelectionModal"
            :users="users"
            :title="userSelectionTitle"
            :preSelectedUserIds="selectedUserIds"
            @close="closeUserSelectionModal"
            @select-users="handleUserSelection"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Sidebar from "./Sidebar.vue";
import ChatWindow from "./ChatWindow.vue";
import UserSelectionModal from "../../Components/UserSelectionModal.vue";

const rooms = ref([]);
const users = ref([]);
const messages = ref([]);
const selectedRoom = ref(null);
const selectedUser = ref(null);
const currentUser = ref(null);

// Fetch rooms and select 'All Talk' by default
onMounted(async () => {
    // Agora usando autenticação padrão do Jetstream com cookies

    // Get current authenticated user
    try {
        const userRes = await fetch("/user", {
            credentials: "include",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });
        console.log("USER RESPONSE STATUS:", userRes.status);
        if (userRes.ok) {
            currentUser.value = await userRes.json();
            console.log("USUÁRIO ATUAL:", currentUser.value);
        } else {
            console.error(
                "Failed to fetch current user:",
                await userRes.text()
            );
        }
    } catch (userError) {
        console.error("ERRO AO BUSCAR USUÁRIO:", userError);
    }

    // Get rooms
    try {
        console.log("BUSCANDO SALAS...");
        const roomsRes = await fetch("/rooms", {
            credentials: "include",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });
        console.log("ROOMS RESPONSE STATUS:", roomsRes.status);

        if (roomsRes.ok) {
            const data = await roomsRes.json();
            console.log("SALAS RECEBIDAS:", data);
            rooms.value = data;

            // Select 'All Talk' room if it exists
            const allTalk = rooms.value.find((r) => r.name === "All Talk");
            console.log("SALA ALL TALK:", allTalk);

            if (allTalk) {
                console.log("SELECIONANDO ALL TALK...");
                await selectRoom(allTalk);
                console.log("SALA SELECIONADA:", selectedRoom.value);
            } else {
                console.error("ALL TALK NÃO ENCONTRADA");
            }
        } else {
            console.error("Failed to fetch rooms:", await roomsRes.text());
        }
    } catch (roomsError) {
        console.error("ERRO AO BUSCAR SALAS:", roomsError);
    }

    // Fetch users for DMs (excluding current user)
    try {
        const usersRes = await fetch("/users", {
            credentials: "same-origin",
        });
        console.log("USERS RESPONSE STATUS:", usersRes.status);

        if (usersRes.ok) {
            const allUsers = await usersRes.json();
            console.log("USUÁRIOS RECEBIDOS:", allUsers);
            users.value = allUsers.filter(
                (user) => user.id !== currentUser.value?.id
            );
        } else {
            console.error("Failed to fetch users:", await usersRes.text());
        }
    } catch (usersError) {
        console.error("ERRO AO BUSCAR USUÁRIOS:", usersError);
    }
});

async function selectRoom(room) {
    selectedRoom.value = room;
    selectedUser.value = null;

    // Fetch messages for this room
    try {
        const res = await fetch(`/messages?room_id=${room.id}`, {
            credentials: "same-origin",
        });
        if (res.ok) {
            messages.value = await res.json();
        } else {
            messages.value = [];
            console.error("Failed to fetch room messages:", await res.text());
        }
    } catch (e) {
        messages.value = [];
        console.error("Error fetching room messages:", e);
    }
}

async function selectDM(user) {
    selectedUser.value = user;
    selectedRoom.value = null;

    // Fetch messages for this DM
    try {
        const res = await fetch(`/messages?to_user_id=${user.id}`, {
            credentials: "same-origin",
        });
        if (res.ok) {
            messages.value = await res.json();
        } else {
            messages.value = [];
            console.error("Failed to fetch DM messages:", await res.text());
        }
    } catch (e) {
        messages.value = [];
        console.error("Error fetching DM messages:", e);
    }
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        let cookieValue = parts.pop().split(";").shift();
        // For XSRF-TOKEN, Laravel encodes it with URL encoding, so we need to decode it
        if (name === "XSRF-TOKEN") {
            cookieValue = decodeURIComponent(cookieValue);
        }
        return cookieValue;
    }
    return null;
}

// Modal states
const showUserSelectionModal = ref(false);
const newRoomName = ref("");
const userSelectionTitle = ref("Select Users");
const selectedUserIds = ref([]);
const currentRoomForUsers = ref(null);

function openCreateRoomModal() {
    if (!currentUser.value || !currentUser.value.id) {
        alert(
            "Usuário não carregado. Faça login novamente ou recarregue a página."
        );
        return;
    }

    const roomName = prompt("Enter room name:", "");
    if (roomName && roomName.trim()) {
        newRoomName.value = roomName.trim();
        currentRoomForUsers.value = null; // Estamos criando uma nova sala
        userSelectionTitle.value = `Select Users for ${roomName.trim()}`;
        selectedUserIds.value = [currentUser.value.id]; // Pré-seleciona o usuário atual
        showUserSelectionModal.value = true;
    }
}

function closeUserSelectionModal() {
    showUserSelectionModal.value = false;
}

function openAddUsersToRoom(room) {
    currentRoomForUsers.value = room;
    userSelectionTitle.value = `Add Users to ${room.name}`;
    // Pré-seleciona os usuários que já estão na sala
    if (room.users && Array.isArray(room.users)) {
        selectedUserIds.value = room.users.map((user) => user.id);
    } else {
        selectedUserIds.value = [];
    }
    showUserSelectionModal.value = true;
}

function handleUserSelection(userIds) {
    if (currentRoomForUsers.value) {
        // Se estamos adicionando usuários a uma sala existente
        addUsersToRoom(currentRoomForUsers.value, userIds);
    } else {
        // Se estamos criando uma nova sala
        createRoomWithUsers(userIds);
    }
}

async function addUsersToRoom(room, userIds) {
    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        const res = await fetch(`/rooms/${room.id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
            body: JSON.stringify({
                users: userIds,
            }),
            credentials: "include",
        });

        if (res.ok) {
            const updatedRoom = await res.json();
            // Atualiza a sala na lista
            const index = rooms.value.findIndex((r) => r.id === updatedRoom.id);
            if (index !== -1) {
                rooms.value[index] = updatedRoom;
            }
            alert("Users updated successfully!");
        } else {
            const errorText = await res.text();
            console.error("Failed to update room users:", errorText);
            alert("Error updating room users: " + errorText);
        }
    } catch (error) {
        console.error("Error updating room users:", error);
        alert("Error updating room users. Please try again.");
    }
}

async function createRoomWithUsers(selectedUserIds) {
    if (!currentUser.value || !currentUser.value.id) {
        alert(
            "Usuário não carregado. Faça login novamente ou recarregue a página."
        );
        return;
    }

    // Always include the current user
    if (!selectedUserIds.includes(currentUser.value.id)) {
        selectedUserIds.push(currentUser.value.id);
    }

    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        const res = await fetch("/rooms", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
            body: JSON.stringify({
                name: newRoomName.value,
                avatar: null,
                users: selectedUserIds,
            }),
            credentials: "include",
        });
        if (res.ok) {
            const newRoom = await res.json();
            rooms.value.push(newRoom);
            selectRoom(newRoom);
        } else {
            const errorText = await res.text();
            console.error("Failed to create room:", errorText);
            alert("Erro ao criar sala: " + errorText);
        }
    } catch (error) {
        console.error("Error creating room:", error);
        alert("Error creating room. Please try again.");
    }
}

async function sendMessage(content) {
    try {
        const payload = selectedRoom.value
            ? { content, room_id: selectedRoom.value.id }
            : { content, to_user_id: selectedUser.value.id };

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        const response = await fetch("/messages", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
            credentials: "include",
        });

        if (response.ok) {
            const newMessage = await response.json();
            messages.value.push(newMessage);
        } else {
            console.error("Failed to send message:", await response.text());
        }
    } catch (error) {
        console.error("Error sending message:", error);
    }
}
</script>
