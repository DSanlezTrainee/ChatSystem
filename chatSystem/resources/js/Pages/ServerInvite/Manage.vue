<template>
    <div class="flex h-screen bg-black">
        <Sidebar
            :server="server"
            :rooms="rooms"
            :users="users"
            :selectedRoom="null"
            :selectedUser="null"
            :currentUser="currentUser"
            :jetstream="$page.props.jetstream"
        />

        <div class="flex-1 flex flex-col m">
            <header
                class="bg-gray-900 text-white p-4 border-b border-gray-800 flex items-center justify-between"
            >
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">Manage Server Invites</h1>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white">
                <div class="max-w-3xl mx-auto mt-5">
                    <div class="bg-gray-900 p-6 rounded-lg shadow-lg mb-6">
                        <div
                            class="flex items-center gap-4 mb-6 justify-center"
                        >
                            <button
                                @click="triggerFileInput"
                                class="bg-gray-800 rounded-full p-2 flex items-center justify-center mr-2"
                                type="button"
                                style="height: 40px; width: 40px"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 7h2l2-3h6l2 3h2a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z"
                                    />
                                    <circle cx="12" cy="13" r="4" />
                                </svg>
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="onFileChange"
                                />
                            </button>

                            <!-- Imagem ao centro -->
                            <img
                                :src="server.avatar || '/default-server.png'"
                                alt="Server Avatar"
                                class="w-16 h-16 rounded-2xl object-cover border border-gray-600 mx-2"
                                style="box-shadow: 0 0 8px #222"
                            />

                            <!-- No separate avatar update button -->
                        </div>

                        <div
                            class="flex items-center gap-4 mb-6 justify-center"
                        >
                            <input
                                v-model="serverName"
                                type="text"
                                class="w-80 bg-gray-700 text-white border border-gray-600 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center"
                                style="min-width: 180px"
                                placeholder="Server name"
                            />
                            <button
                                @click="updateServerName"
                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
                                :disabled="isUpdatingName"
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
                                        d="m4.5 12.75 6 6 9-13.5"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div v-if="pendingInvite" class="mb-4">
                            <div class="bg-gray-800 p-2 rounded-lg mb-2">
                                <h4 class="font-bold mb-2 text-center">
                                    Share this to invite people
                                </h4>
                                <div
                                    class="flex flex-col md:flex-row md:items-center justify-between gap-3"
                                >
                                    <div class="flex-1">
                                        <input
                                            type="text"
                                            :value="pendingInvite.invite_link"
                                            readonly
                                            class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            @click="
                                                copyInviteLink(
                                                    pendingInvite.invite_link
                                                )
                                            "
                                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
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
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click="generateNewInviteLink"
                                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
                                            :disabled="isGenerating"
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
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-400 mt-2">
                                    <span
                                        >Crated at:
                                        {{
                                            formatDate(pendingInvite.created_at)
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-gray-400 mb-4">
                            No active invite link at the moment.
                        </div>
                    </div>

                    <!-- Notification -->
                    <div
                        v-if="showCopiedNotification"
                        class="fixed bottom-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-lg"
                    >
                        Link copied to clipboard!
                    </div>

                    <!-- User List with Delete -->
                    <div class="bg-gray-900 p-6 rounded-lg shadow-lg mb-6 mt-8">
                        <h2 class="text-lg font-bold mb-4 text-center">
                            Server Users
                        </h2>
                        <ul>
                            <li
                                v-for="user in localUsers"
                                :key="user.id"
                                class="flex items-center justify-between py-2 border-b border-gray-800"
                            >
                                <div class="flex items-center gap-3">
                                    <img
                                        :src="
                                            user.profile_photo_url ||
                                            '/default-avatar.png'
                                        "
                                        alt="Avatar"
                                        class="w-8 h-8 rounded-full border border-gray-600"
                                    />
                                    <span class="font-medium">{{
                                        user.name
                                    }}</span>
                                    <span class="text-xs text-gray-400 ml-2">{{
                                        user.email
                                    }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="togglePermission(user)"
                                        :class="[
                                            'px-2 py-1 rounded flex items-end',
                                            user.permission === 'admin'
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-white text-blue-500 border border-blue-500',
                                        ]"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="#FFD700"
                                            class="w-5 h-5"
                                        >
                                            <path
                                                d="M4 17L2 7l5 4 5-9 5 9 5-4-2 10H4zm0 2h16v2H4v-2z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="deleteUser(user)"
                                        class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded flex items-center"
                                        :disabled="deletingUserId === user.id"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-white"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        </ul>
                        <div
                            v-if="localUsers.length === 0"
                            class="text-gray-400 text-center mt-4"
                        >
                            No users found.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import Sidebar from "@/Pages/Chat/Sidebar.vue";
import { watch } from "vue";
import { reactive } from "vue";

// Configure axios to include CSRF token for Laravel
axios.defaults.headers.common["X-CSRF-TOKEN"] = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

// Function to initialize user permissions from server/backend data
// and ensure they are properly set for UI rendering
function initUserPermissions(usersList) {
    if (!Array.isArray(usersList)) return [];

    // Try to get saved permissions from localStorage
    let savedPermissions = {};
    try {
        savedPermissions = JSON.parse(
            localStorage.getItem("userPermissions") || "{}"
        );
        console.log("Loaded saved permissions:", savedPermissions);
    } catch (err) {
        console.error("Error loading saved permissions:", err);
    }

    return usersList.map((user) => {
        // Use saved permission from localStorage if available, otherwise use server value
        const effectivePermission =
            savedPermissions[user.id] || user.permission;

        return {
            ...user,
            // Ensure permission is exactly 'admin' or 'user' for class binding
            permission: effectivePermission === "admin" ? "admin" : "user",
        };
    });
}

const props = defineProps({
    server: Object,
    pendingInvite: Object,
    usedInvites: Array,
    rooms: Array,
    users: Array,
    currentUser: Object,
});

const isGenerating = ref(false);
const showCopiedNotification = ref(false);
const isUpdatingName = ref(false);
const serverName = ref(props.server?.name || "");
const selectedFile = ref(null);

const deletingUserId = ref(null);
// Create a reactive copy of the users array that we'll use for rendering
// Properly initialize users with permission data
const localUsers = reactive(initUserPermissions(props.users));

function deleteUser(user) {
    if (!user || !user.id) return;
    if (
        !confirm(
            `Are you sure you want to delete user '${user.name}'? This action cannot be undone.`
        )
    )
        return;

    // Set deleting state
    deletingUserId.value = user.id;

    // First remove from local array for immediate UI feedback
    const index = localUsers.findIndex((u) => u.id === user.id);
    if (index !== -1) {
        localUsers.splice(index, 1);
    }

    // Then send request to server
    router.delete(
        `/users/${user.id}`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                console.log(`User ${user.name} deleted successfully`);
            },
            onError: (error) => {
                console.error("Error deleting user:", error);
                alert("Failed to delete user");

                // Add the user back to localUsers if deletion failed
                if (index !== -1) {
                    localUsers.splice(index, 0, user);
                }
            },
            onFinish: () => {
                deletingUserId.value = null;
            },
        }
    );
}

watch(
    () => props.server,
    (newServer) => {
        if (newServer) {
            serverName.value = newServer.name || "";
            console.log("Server name updated to:", serverName.value);
        }
    },
    { immediate: true }
);

// Update localUsers when props.users changes
watch(
    () => props.users,
    (newUsers) => {
        if (Array.isArray(newUsers)) {

            localUsers.splice(0, localUsers.length);
            newUsers.forEach((user) => {
                localUsers.push({ ...user });
            });
            console.log("Local users updated:", localUsers);
        }
    },
    { immediate: true, deep: true }
);

const fileInput = ref(null);
function triggerFileInput() {
    fileInput.value.click();
}

function updateServerName() {
    if (!serverName.value.trim()) return;
    isUpdatingName.value = true;

    if (!props.server || !props.server.id) {
        isUpdatingName.value = false;
        return;
    }

    const formData = new FormData();
    formData.append("name", serverName.value);

    if (selectedFile.value) {
        formData.append("avatar", selectedFile.value);
    }

    formData.append("_method", "PUT");

    axios
        .post(`/servers/${props.server.id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        })
        .then((response) => {
            console.log("Server update response:", response.data);
            if (response.data && response.data.server) {
                props.server.name = response.data.server.name;

                if (response.data.server.avatar) {
                    props.server.avatar = response.data.server.avatar;
                }
                selectedFile.value = null;
            }
            isUpdatingName.value = false;
            alert("Server updated successfully");
        })
        .catch((error) => {
            console.error(
                "Server update error:",
                error.response?.data || error
            );
            isUpdatingName.value = false;
            alert("Failed to update server");
        });
}

async function generateNewInviteLink() {
    isGenerating.value = true;
    try {
        const response = await axios.post(
            "/server-invite/generate",
            {},
            {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                    "X-Inertia": "false",
                },
            }
        );
        if (response.data && response.data.invite_link) {
            props.pendingInvite = {
                token: response.data.token,
                invite_link: response.data.invite_link,
                created_at: new Date().toISOString(),
            };
            router.reload({ only: ["pendingInvite"] });
        }
    } catch (error) {
        console.error("Erro ao gerar link:", error);
    } finally {
        isGenerating.value = false;
    }
}

function copyInviteLink(link) {
    try {
        const textarea = document.createElement("textarea");
        textarea.value = link;
        textarea.style.position = "fixed";
        textarea.style.left = "-999999px";
        textarea.style.top = "-999999px";
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();

        const success = document.execCommand("copy");
        document.body.removeChild(textarea);

        if (success) {
            console.log("Link copied successfully!");
            showCopiedNotification.value = true;
            setTimeout(() => {
                showCopiedNotification.value = false;
            }, 3000);
        } else {
            console.error("Failed to copy link");
        }
    } catch (err) {
        console.error("Error copying link:", err);
        navigator.clipboard
            .writeText(link)
            .then(() => {
                showCopiedNotification.value = true;
                setTimeout(() => {
                    showCopiedNotification.value = false;
                }, 3000);
            })
            .catch((err) => {
                console.error("Error copying with clipboard API:", err);
            });
    }
}

function formatDate(dateString) {
    if (!dateString) return "";
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }).format(date);
}

function onFileChange(e) {
    const file = e.target.files[0];
    if (!file) return;

    selectedFile.value = file;

    const reader = new FileReader();
    reader.onload = (ev) => {
        props.server.avatar = ev.target.result;
    };
    reader.readAsDataURL(file);
}

function togglePermission(user) {
    const originalPermission = user.permission;

    user.permission = user.permission === "admin" ? "user" : "admin";
    console.log(
        `Toggling ${user.name} from ${originalPermission} to ${user.permission}`
    );

    axios
        .post(
            `/users/${user.id}/toggle-permission`,
            {}, 
            {
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            }
        )
        .then((response) => {
            console.log("Toggle permission successful:", response.data);

            try {
                const storedPermissions = JSON.parse(
                    localStorage.getItem("userPermissions") || "{}"
                );
                storedPermissions[user.id] = user.permission;
                localStorage.setItem(
                    "userPermissions",
                    JSON.stringify(storedPermissions)
                );
                console.log(
                    "Saved permissions to localStorage:",
                    storedPermissions
                );
            } catch (err) {
                console.error("Error saving to localStorage:", err);
            }
        })
        .catch((error) => {
            console.error("Toggle permission error:", error);

            user.permission = originalPermission;
            alert("Failed to change permission");
        });

}

// Update localUsers when props.users changes
watch(
    () => props.users,
    (newUsers) => {
        if (Array.isArray(newUsers)) {
            localUsers.splice(0, localUsers.length);
            const formattedUsers = initUserPermissions(newUsers);
            formattedUsers.forEach((user) => localUsers.push(user));
            console.log("Local users updated:", localUsers);
        }
    },
    { immediate: true, deep: true }
);
</script>
