<template>
    <aside
        class="w-64 bg-gray-900 text-white h-full flex flex-col border-r border-gray-800"
    >
        <div class="p-4 font-bold text-lg border-b border-gray-800">
            {{ server?.name || "Chat System" }}
            <img
                v-if="server?.avatar"
                :src="server.avatar"
                alt="Server Avatar"
                class="h-8 w-8 rounded-full inline-block ml-2"
            />
        </div>
        <div class="flex-1 overflow-y-auto">
            <div class="mt-4">
                <div class="px-4 text-xs text-gray-400 uppercase mb-2">
                    Rooms
                </div>
                <ul>
                    <li
                        v-for="room in rooms"
                        :key="room.id"
                        :class="[
                            'px-4 py-2 rounded hover:bg-gray-800',
                            selectedRoom && selectedRoom.id === room.id
                                ? 'bg-gray-800'
                                : '',
                        ]"
                    >
                        <div class="flex items-center justify-between">
                            <div
                                class="cursor-pointer flex-grow"
                                @click="goToRoom(room.id)"
                            >
                                <span class="font-medium">{{ room.name }}</span>
                            </div>
                            <button
                                v-if="currentUser && currentUser.is_admin"
                                @click.stop="goToAddUsers(room.id)"
                                class="text-gray-400 hover:text-white focus:outline-none"
                                aria-label="Add users to room"
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
                        </div>
                    </li>
                </ul>
            </div>
            <div v-if="currentUser && currentUser.is_admin">
                <div class="p-4 border-t border-gray-800">
                    <div class="flex flex-col gap-3">
                        <button
                            v-if="currentUser && currentUser.is_admin"
                            @click="goToCreateRoom()"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-7"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 4.5v15m7.5-7.5h-15"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <div class="px-4 text-xs text-gray-400 uppercase mb-2">
                    Direct Messages
                </div>
                <ul class="flex flex-col gap-2">
                    <li
                        v-for="user in users"
                        :key="user.id"
                        @click="goToDirectMessage(user.id)"
                        :class="[
                            'cursor-pointer px-4 py-2 rounded hover:bg-gray-800 flex items-center gap-2',
                            selectedUser && selectedUser.id === user.id
                                ? 'bg-gray-800'
                                : '',
                        ]"
                    >
                        <div class="flex items-center gap-2">
                            <template
                                v-if="user.profile_photo_url || user.avatar"
                            >
                                <img
                                    :src="user.profile_photo_url || user.avatar"
                                    alt="Avatar"
                                    class="h-8 w-8 rounded-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <div
                                    class="h-8 w-8 rounded-full bg-gray-800 flex items-center justify-center"
                                >
                                    <span
                                        class="text-xs font-semibold text-white"
                                    >
                                        {{ user.name.charAt(0) }}
                                    </span>
                                </div>
                            </template>
                            <span class="font-medium text-xs text-left">{{
                                user.name
                            }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Server Invite Button (Bottom Left) -->
        <div
            v-if="currentUser && currentUser.is_admin"
            class="p-4 border-t border-gray-800"
        >
            <button @click="goToServerInvites" class="settings-btn">
                <svg
                    width="32"
                    height="32"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09A1.65 1.65 0 0 0 9 4.6V4a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"
                    />
                </svg>
            </button>
        </div>
    </aside>
    <div v-if="currentUser">
        <UserMenu :user="currentUser" :jetstream="jetstream" />
    </div>
</template>
<script setup>
import { visitRoute } from "@/router-config";
import UserMenu from "@/Components/UserMenu.vue";

function goToRoom(id) {
    visitRoute(`/rooms/${id}`);
}

function goToDirectMessage(userId) {
    visitRoute(`/chat/users/${userId}`);
}

function goToCreateRoom() {
    visitRoute("/rooms/create");
}

function goToAddUsers(roomId) {
    visitRoute(`/rooms/${roomId}/users`);
}

function goToServerInvites() {
    console.log("Clicado no botão de convites");
    visitRoute("/server-invite/manage");
}

defineProps({
    server: Object,
    rooms: Array,
    users: Array,
    selectedRoom: Object,
    selectedUser: Object,
    currentUser: Object,
    jetstream: Object,
});
</script>

<style scoped>
.settings-btn {
    color: white;
    bottom: 20px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}
</style>
