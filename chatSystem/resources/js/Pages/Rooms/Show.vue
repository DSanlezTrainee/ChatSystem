<template>
    <div class="flex h-screen bg-black">
        <Sidebar
            :rooms="rooms"
            :users="users"
            :selectedRoom="room"
            :selectedUser="null"
            :currentUser="$page.props.auth.user"
            :jetstream="$page.props.jetstream"
        />
        <div class="flex-1 flex flex-col">
            <div
                class="bg-gray-900 text-white p-4 border-b border-gray-800 flex items-center justify-between"
            >
                <h1 class="text-xl font-bold">{{ room.name }}</h1>
            </div>
            <div
                class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white"
                ref="messagesContainer"
            >
                <div class="mb-6">
                    <h2 class="text-lg font-bold mb-2">Users:</h2>
                    <div class="flex flex-wrap gap-2">
                        <div
                            v-for="user in room.users"
                            :key="user.id"
                            class="bg-gray-700 text-white px-3 py-1 rounded-full text-sm"
                        >
                            {{ user.name }}
                            <!-- Mostrar informação de joined_at se disponível -->
                            <span
                                v-if="user.pivot && user.pivot.joined_at"
                                class="text-xs text-gray-300 ml-1"
                            >
                            </span>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <h2 class="text-lg font-bold mb-2">Messages:</h2>
                    <div v-if="room.messages && room.messages.length > 0">
                        <div
                            v-for="message in room.messages"
                            :key="message.id"
                            :class="[
                                'flex items-start gap-3 mb-4',
                                message.user_id === $page.props.auth.user.id
                                    ? 'justify-start'
                                    : 'justify-end',
                            ]"
                        >
                            <div
                                :class="[
                                    'rounded-lg p-3 max-w-md group relative',
                                    message.user_id === $page.props.auth.user.id
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-700 text-white',
                                ]"
                            >
                                <div
                                    class="font-bold text-sm flex items-center gap-2"
                                >
                                    <img
                                        :src="getUserAvatar(message.user_id)"
                                        alt=""
                                        class="w-8 h-8 rounded-full"
                                    />
                                    <span>{{
                                        getUserName(message.user_id)
                                    }}</span>
                                    <div class="mr-6">
                                        <div
                                            v-if="canEditMessage(message)"
                                            class="absolute top-0 right-0 opacity-0 group-hover:opacity-100 transition-opacity message-menu"
                                        >
                                            <button
                                                @click="
                                                    toggleMessageMenu(
                                                        message.id
                                                    )
                                                "
                                                class="text-gray-400 hover:text-white p-1 rounded"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                                                    />
                                                </svg>
                                            </button>

                                            <!-- Menu dropdown -->
                                            <div
                                                v-if="
                                                    activeMessageMenu ===
                                                    message.id
                                                "
                                                class="absolute right-0 mt-1 bg-gray-800 border border-gray-700 rounded shadow-lg z-10 w-40 message-menu"
                                            >
                                                <div class="py-1">
                                                    <button
                                                        @click="
                                                            editMessage(message)
                                                        "
                                                        class="w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 mr-2"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                            />
                                                        </svg>
                                                        Edit Message
                                                    </button>
                                                    <button
                                                        @click="
                                                            deleteMessage(
                                                                message
                                                            )
                                                        "
                                                        class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-gray-700 flex items-center"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 mr-2"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            />
                                                        </svg>
                                                        Delete Message
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2 relative">
                                    <template v-if="message.file_path">
                                        <template
                                            v-if="isImage(message.file_path)"
                                        >
                                            <a
                                                :href="
                                                    getFileUrl(
                                                        message.file_path
                                                    )
                                                "
                                                target="_self"
                                            >
                                                <img
                                                    :src="
                                                        getFileUrl(
                                                            message.file_path
                                                        )
                                                    "
                                                    alt="imagem"
                                                    class="max-w-xs max-h-48 rounded border border-gray-600 mb-2 cursor-pointer"
                                                />
                                            </a>
                                        </template>
                                        <template v-else>
                                            <a
                                                :href="
                                                    getFileUrl(
                                                        message.file_path
                                                    )
                                                "
                                                target="_blank"
                                                class="text-blue-300 underline"
                                            >
                                                {{
                                                    message.content ||
                                                    "File sent"
                                                }}
                                            </a>
                                        </template>
                                    </template>

                                    <template v-else>
                                        <div
                                            v-if="
                                                editingMessage &&
                                                editingMessage.id === message.id
                                            "
                                        >
                                            <textarea
                                                v-model="editingMessage.content"
                                                class="w-full bg-gray-900 text-white p-2 rounded border border-gray-600 "
                                                @keydown.enter.prevent="
                                                    updateMessage
                                                "
                                            ></textarea>
                                            <div
                                                class="flex justify-end mt-1 space-x-2"
                                            >
                                                <button
                                                    @click="cancelEdit"
                                                    class="px-2 py-1 text-xs rounded bg-gray-700 text-white hover:bg-gray-600"
                                                >
                                                    Cancel
                                                </button>
                                                <button
                                                    @click="updateMessage"
                                                    class="px-2 py-1 text-xs rounded bg-gray-700 text-white hover:bg-blue-500"
                                                >
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div v-else>
                                            {{ message.content }}
                                            <span
                                                v-if="
                                                    message.updated_at !==
                                                    message.created_at
                                                "
                                                class="text-xs text-gray-400 ml-1"
                                                >(edited)</span
                                            >
                                        </div>
                                    </template>
                                </div>

                                <div class="text-xs opacity-70 mt-1">
                                    {{
                                        new Date(
                                            message.created_at
                                        ).toLocaleString()
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-400">
                        No messages in this room yet.
                    </div>
                </div>
            </div>

            <footer class="p-4 border-t border-gray-800">
                <form
                    @submit.prevent="sendMessage"
                    class="flex gap-2 items-center"
                >
                    <div class="relative flex-1">
                        <input
                            v-model="newMessage"
                            type="text"
                            placeholder="Type your message..."
                            class="w-full bg-gray-700 text-white rounded px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <label
                            class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer flex items-center"
                            title="Send File"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-blue-400 hover:text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 10-5.656-5.656l-6.586 6.586a6 6 0 108.486 8.486"
                                />
                            </svg>
                            <input
                                type="file"
                                class="hidden"
                                @change="handleFileUpload"
                                ref="fileInput"
                            />
                        </label>
                    </div>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :disabled="!newMessage.trim()"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"
                            />
                        </svg>
                    </button>
                </form>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch, onUnmounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import Sidebar from "@/Pages/Chat/Sidebar.vue";
import router from "@/router-config";

const props = defineProps({
    room: Object,
    rooms: Array,
    users: Array,
    currentUser: Object,
});

const page = usePage();
const newMessage = ref("");
const messagesContainer = ref(null);
const fileInput = ref(null);
const activeMessageMenu = ref(null);
const editingMessage = ref(null);

function handleFileUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append("file", file);
    formData.append("room_id", props.room.id);

    fetch("/messages/upload", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((res) => res.json())
        .then((msg) => {
            router.reload({ preserveScroll: true, preserveState: false });
            scrollToBottom();
        })
        .catch((err) => {
            alert("Error uploading file");
        });
    // Clear input
    fileInput.value.value = "";
}

function getFileUrl(path) {
    return `/storage/${path}`;
}

function isImage(path) {
    return /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(path);
}

function scrollToBottom() {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop =
                messagesContainer.value.scrollHeight;
        }
    });
}

function sendMessage() {
    if (!newMessage.value.trim()) return;

    router.post(
        "/messages",
        {
            content: newMessage.value,
            room_id: props.room.id,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                newMessage.value = "";

                console.log("Reloading page to refresh messages...");
                router.reload({
                    preserveScroll: true,
                    preserveState: false,
                });
                scrollToBottom();
            },
        }
    );
}

watch(
    () => props.room.messages,
    () => {
        scrollToBottom();
    }
);

onMounted(() => {
    document.addEventListener("click", closeMenuOnOutsideClick);
    scrollToBottom();
});

onUnmounted(() => {
    document.removeEventListener("click", closeMenuOnOutsideClick);
});

function getUserName(userId) {
    const user = props.room.users.find((user) => user.id === userId);
    return user ? user.name : "Unknown User";
}

function getUserAvatar(userId) {
    const user = props.room.users.find((user) => user.id === userId);
    if (!user) return "";
    if (user.avatar) return user.avatar;
    if (user.profile_photo_url) return user.profile_photo_url;
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`;
}

// Fechar menu quando clicar fora
const closeMenuOnOutsideClick = (e) => {
    if (activeMessageMenu.value && !e.target.closest(".message-menu")) {
        activeMessageMenu.value = null;
    }
};

// Verificar se o usuário pode editar a mensagem (é autor ou admin)
function canEditMessage(message) {
    // Try to get auth user from Inertia page props first
    const authUser = page.props.auth?.user;
    // Fallback to props.currentUser if auth is not available
    const currentUser = authUser ||
        props.currentUser || { id: null, permission: null };
    return (
        message.user_id === currentUser.id || currentUser.permission === "admin"
    );
}

// Abrir/fechar menu de uma mensagem específica
function toggleMessageMenu(messageId) {
    if (activeMessageMenu.value === messageId) {
        activeMessageMenu.value = null;
    } else {
        activeMessageMenu.value = messageId;
    }
}

// Iniciar edição de mensagem
function editMessage(message) {
    editingMessage.value = {
        id: message.id,
        content: message.content,
    };
    activeMessageMenu.value = null;

    // Focar no textarea após renderização
    nextTick(() => {
        const textarea = document.querySelector("textarea");
        if (textarea) {
            textarea.focus();
        }
    });
}

// Cancelar edição
function cancelEdit() {
    editingMessage.value = null;
}

// Salvar mensagem editada
function updateMessage() {
    if (!editingMessage.value || !editingMessage.value.content.trim()) {
        return;
    }

    router.put(
        `/messages/${editingMessage.value.id}`,
        { content: editingMessage.value.content },
        {
            preserveScroll: true,
            onSuccess: () => {
                editingMessage.value = null;
                router.reload({ preserveScroll: true });
            },
        }
    );
}

// Excluir mensagem
function deleteMessage(message) {
    if (confirm("Are you sure you want to delete this message?")) {
        router.delete(`/messages/${message.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                activeMessageMenu.value = null;
                router.reload({ preserveScroll: true });
            },
        });
    }
}
</script>

<style scoped>
.message-menu {
    transition: opacity 0.2s ease-in-out;
}

.group:hover .message-menu {
    opacity: 1;
}

/* Ensure message menu appears above other content */
.message-menu {
    z-index: 10;
}

.message-edit-textarea {
    white-space: pre-wrap;
    word-break: break-word;
    resize: vertical;
    min-height: 48px;
    padding: 8px;
}
</style>
