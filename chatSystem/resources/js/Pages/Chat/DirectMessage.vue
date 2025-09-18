<template>
    <div class="flex h-screen bg-black">
        <Sidebar
            :rooms="rooms"
            :users="users"
            :selectedRoom="null"
            :selectedUser="toUser"
            :currentUser="currentUser"
            :jetstream="$page.props.jetstream"
        />
        <div class="flex-1 flex flex-col">
            <div
                class="bg-gray-900 text-white p-4 border-b border-gray-800 flex items-center"
            >
                <img
                    :src="
                        toUser.avatar ||
                        toUser.profile_photo_url ||
                        `https://ui-avatars.com/api/?name=${encodeURIComponent(
                            toUser.name
                        )}`
                    "
                    alt=""
                    class="h-10 w-10 rounded-full mr-3"
                />
                <h1 class="text-xl font-bold">{{ toUser.name }}</h1>
                <span
                    v-if="toUser.status"
                    class="text-xs ml-3 px-2 py-1 rounded-full"
                    :class="{
                        'bg-green-600': toUser.status === 'active',
                        'bg-gray-600': toUser.status === 'inactive',
                    }"
                >
                    {{ toUser.status === "active" ? "Online" : "Offline" }}
                </span>
            </div>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white">
                <div class="space-y-4">
                    <div
                        v-if="messages.length === 0"
                        class="text-center text-gray-400 mt-8"
                    >
                        <p>Nenhuma mensagem ainda. Comece uma conversa!</p>
                    </div>

                    <div
                        v-for="message in messages"
                        :key="message.id"
                        :class="[
                            'flex items-start gap-2',
                            message.user_id === currentUser.id
                                ? 'justify-end'
                                : 'justify-start',
                        ]"
                    >
                        <div
                            v-if="message.user_id !== currentUser.id"
                            class="flex-shrink-0"
                        >
                            <img
                                :src="
                                    toUser.avatar ||
                                    toUser.profile_photo_url ||
                                    `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                        toUser.name
                                    )}`
                                "
                                alt=""
                                class="w-8 h-8 rounded-full"
                            />
                        </div>
                        <div
                            :class="[
                                'rounded-lg p-3 max-w-md',
                                message.user_id === currentUser.id
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-700 text-white',
                            ]"
                        >
                            <div>{{ message.content }}</div>
                            <div class="text-xs opacity-70 mt-1">
                                {{
                                    new Date(
                                        message.created_at
                                    ).toLocaleTimeString()
                                }}
                            </div>
                        </div>
                        <div
                            v-if="message.user_id === currentUser.id"
                            class="flex-shrink-0"
                        >
                            <img
                                :src="
                                    currentUser.avatar ||
                                    currentUser.profile_photo_url ||
                                    `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                        currentUser.name
                                    )}`
                                "
                                alt=""
                                class="w-8 h-8 rounded-full"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <footer class="p-4 border-t border-gray-800">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input
                        v-model="newMessage"
                        type="text"
                        placeholder="Digite sua mensagem..."
                        class="flex-1 bg-gray-700 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :disabled="!newMessage.trim()"
                    >
                        Send
                    </button>
                </form>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import router, { reloadPage } from "@/router-config";
import Sidebar from "@/Pages/Chat/Sidebar.vue";

const props = defineProps({
    rooms: Array,
    users: Array,
    messages: Array,
    toUser: Object,
    currentUser: Object,
});

const newMessage = ref("");

function sendMessage() {
    if (!newMessage.value.trim()) return;

    router.post(
        "/messages",
        {
            content: newMessage.value,
            to_user_id: props.toUser.id,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                newMessage.value = "";
                // Refresh the messages by revisiting the current page
                reloadPage({ only: ["messages"] });
            },
        }
    );
}
</script>
