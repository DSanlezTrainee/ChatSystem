<template>
    <div class="flex h-screen bg-black">
        <Sidebar
            :rooms="rooms"
            :users="[]"
            :selectedRoom="null"
            :selectedUser="null"
            :currentUser="$page.props.auth.user"
            :jetstream="$page.props.jetstream"
        />
        <div class="flex-1 flex flex-col">
            <div
                class="bg-gray-900 text-white p-4 border-b border-gray-800 flex items-center justify-between"
            >
                <h1 class="text-xl font-bold">Rooms</h1>
            </div>
            <div class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
                >
                    <div
                        v-for="room in rooms"
                        :key="room.id"
                        class="bg-gray-700 p-4 rounded-lg shadow hover:bg-gray-600 transition cursor-pointer"
                        @click="goToRoom(room.id)"
                    >
                        <h2 class="text-lg font-bold mb-2">{{ room.name }}</h2>
                        <div class="text-sm text-gray-300">
                            {{ room.users.length }} usuários
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import Sidebar from "@/Pages/Chat/Sidebar.vue";

const props = defineProps({
    rooms: Array,
});

function goToRoom(id) {
    router.visit(`/rooms/${id}`, {
        preserveState: false,
        preserveScroll: false,
        only: [],
    });
}
</script>
