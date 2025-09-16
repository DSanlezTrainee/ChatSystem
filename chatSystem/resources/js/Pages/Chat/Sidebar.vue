<template>
    <aside
        class="w-64 bg-gray-900 text-white h-full flex flex-col border-r border-gray-800"
    >
        <div class="p-4 font-bold text-lg border-b border-gray-800">
            Campfire
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
                                @click="$emit('select-room', room)"
                            >
                                <span class="font-medium">{{ room.name }}</span>
                            </div>
                            <button
                                @click.stop="$emit('add-users-to-room', room)"
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
            <div class="mt-6">
                <div class="px-4 text-xs text-gray-400 uppercase mb-2">
                    Direct Messages
                </div>
                <ul>
                    <li
                        v-for="user in users"
                        :key="user.id"
                        @click="$emit('select-dm', user)"
                        :class="[
                            'cursor-pointer px-4 py-2 rounded hover:bg-gray-800',
                            selectedUser && selectedUser.id === user.id
                                ? 'bg-gray-800'
                                : '',
                        ]"
                    >
                        <span class="font-medium">{{ user.name }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="p-4 border-t border-gray-800">
            <div class="flex flex-col items-center gap-3">
                <button
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded"
                    @click="$emit('create-room')"
                >
                    + New Room
                </button>
            </div>
        </div>
    </aside>
    <div v-if="currentUser">
        <UserMenu :user="currentUser" :jetstream="jetstream" />
    </div>
</template>
<script setup>
import UserMenu from "@/Components/UserMenu.vue";

defineProps({
    rooms: Array,
    users: Array,
    selectedRoom: Object,
    selectedUser: Object,
    currentUser: Object,
    jetstream: Object,
});

const emit = defineEmits([
    "select-room",
    "select-dm",
    "create-room",
    "add-users-to-room",
]);
</script>
