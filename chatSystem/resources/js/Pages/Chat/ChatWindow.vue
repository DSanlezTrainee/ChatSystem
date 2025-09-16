<template>
    <div class="flex flex-col h-full bg-gray-950">
        <header class="p-4 border-b border-gray-800 flex items-center">
            <div class="font-bold text-lg text-white">{{ title }}</div>
        </header>
        <main class="flex-1 overflow-y-auto p-4 space-y-2">
            <Message
                v-for="message in messages"
                :key="message.id"
                :message="message"
                :currentUser="currentUser"
            />
        </main>
        <footer class="p-4 border-t border-gray-800">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input
                    v-model="newMessage"
                    type="text"
                    placeholder="Type a message..."
                    class="flex-1 bg-gray-800 text-white rounded px-4 py-2 focus:outline-none"
                />
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                >
                    Send
                </button>
            </form>
        </footer>
    </div>
</template>

<script setup>
import { ref } from "vue";
import Message from "./Message.vue";

defineProps({
    messages: Array,
    title: String,
    currentUser: Object,
});

const emit = defineEmits(["send-message"]);
const newMessage = ref("");

function sendMessage() {
    if (newMessage.value.trim()) {
        emit("send-message", newMessage.value);
        newMessage.value = "";
    }
}
</script>
