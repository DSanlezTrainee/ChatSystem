<template>
    <div class="flex h-screen bg-black">
        <Sidebar
            :rooms="rooms"
            :users="users"
            :selectedRoom="null"
            :selectedUser="null"
            :currentUser="currentUser"
            :jetstream="$page.props.jetstream"
        />

        <div class="flex-1 flex flex-col">
            <header
                class="bg-gray-900 text-white p-4 border-b border-gray-800 flex items-center"
            >
                <h1 class="text-xl font-bold">Create Room</h1>
            </header>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white">
                <div class="max-w-3xl mx-auto">
                    <form
                        @submit.prevent="createRoom"
                        class="bg-gray-900 p-6 rounded-lg shadow-lg"
                    >
                        <div class="mb-6">
                            <label
                                for="roomName"
                                class="block text-white text-lg font-medium mb-2"
                                >Room Name</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                id="roomName"
                                class="w-full bg-gray-800 text-white border border-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Digite o nome da sala"
                                required
                            />
                            <div v-if="errors.name" class="text-red-500 mt-1">
                                {{ errors.name }}
                            </div>
                        </div>

                        <div class="mb-6">
                            <h2 class="text-white text-lg font-medium mb-4">
                                Select Users
                            </h2>

                            <div
                                class="flex items-center justify-between mb-4 pb-2 border-b border-gray-700"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-8 w-8 bg-gray-800 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p
                                            class="text-base font-semibold text-white"
                                        >
                                            All
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <button
                                        type="button"
                                        class="relative inline-flex h-7 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="{
                                            'bg-blue-600': selectAll,
                                            'bg-gray-700': !selectAll,
                                        }"
                                        @click="toggleSelectAll"
                                    >
                                        <span
                                            class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="{
                                                'translate-x-7': selectAll,
                                                'translate-x-0': !selectAll,
                                            }"
                                        ></span>
                                    </button>
                                </div>
                            </div>

                            <div class="bg-gray-900 rounded-lg overflow-hidden">
                                <div
                                    v-if="users.length > 0"
                                    class="divide-y divide-gray-800"
                                >
                                    <div
                                        v-for="user in users"
                                        :key="user.id"
                                        class="flex items-center justify-between py-3 px-2"
                                    >
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10"
                                            >
                                                <img
                                                    :src="
                                                        user.profile_photo_url ||
                                                        `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                                            user.name
                                                        )}&background=6366f1&color=fff`
                                                    "
                                                    alt=""
                                                    class="h-10 w-10 rounded-full"
                                                />
                                            </div>
                                            <div class="ml-3">
                                                <p
                                                    class="text-base font-medium text-white"
                                                >
                                                    {{ user.name }}
                                                </p>
                                                <span
                                                    v-if="user.status"
                                                    class="inline-block h-2 w-2 rounded-full mt-1"
                                                    :class="{
                                                        'bg-green-500':
                                                            user.status ===
                                                            'active',
                                                        'bg-gray-500':
                                                            user.status !==
                                                            'active',
                                                    }"
                                                ></span>
                                                <span
                                                    class="text-xs text-gray-400 ml-1"
                                                    >{{
                                                        user.status === "active"
                                                            ? "Online"
                                                            : "Offline"
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                        <div>
                                            <button
                                                type="button"
                                                class="relative inline-flex h-7 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                                :class="{
                                                    'bg-blue-600': isSelected(
                                                        user.id
                                                    ),
                                                    'bg-gray-700': !isSelected(
                                                        user.id
                                                    ),
                                                }"
                                                @click="toggleUser(user.id)"
                                            >
                                                <span
                                                    class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                    :class="{
                                                        'translate-x-7':
                                                            isSelected(user.id),
                                                        'translate-x-0':
                                                            !isSelected(
                                                                user.id
                                                            ),
                                                    }"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="py-4 text-center text-gray-400"
                                >
                                    No user available
                                </div>
                            </div>
                            <div v-if="errors.users" class="text-red-500 mt-1">
                                {{ errors.users }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <button
                                type="button"
                                @click="cancelCreate"
                                class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :disabled="processing"
                            >
                                <span v-if="processing">Creating...</span>
                                <span v-else>Create Room</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import Sidebar from "@/Pages/Chat/Sidebar.vue";

const props = defineProps({
    rooms: Array,
    users: Array,
    currentUser: Object,
    errors: Object,
});

const form = ref({
    name: "",
    avatar: "",
    users: [],
});

const processing = ref(false);
const selectAll = ref(false);
const errors = ref({});


const isSelected = (userId) => {
    return form.value.users.includes(userId);
};
const toggleUser = (userId) => {
    if (isSelected(userId)) {
        form.value.users = form.value.users.filter((id) => id !== userId);
        selectAll.value = false;
    } else {
        form.value.users.push(userId);
        selectAll.value = form.value.users.length === props.users.length;
    }
};

const toggleSelectAll = () => {
    if (selectAll.value) {
        form.value.users = [];
        selectAll.value = false;
    } else {
        form.value.users = props.users.map((user) => user.id);
        selectAll.value = true;
    }
};

const createRoom = () => {
    processing.value = true;

    if (!form.value.users.includes(props.currentUser.id)) {
        form.value.users.push(props.currentUser.id);
    }

    router.post("/rooms", form.value, {
        onError: (e) => {
            errors.value = e;
            processing.value = false;
        },
    });
};

const cancelCreate = () => {
    window.history.back();
};
</script>
