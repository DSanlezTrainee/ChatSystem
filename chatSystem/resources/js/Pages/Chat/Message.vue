<template>
    <div
        :class="[
            'flex items-start gap-2',
            message.user.id === currentUser.id
                ? 'justify-start'
                : 'justify-start',
        ]"
    >
        <img
            :src="
                message.user.avatar ||
                'https://ui-avatars.com/api/?name=' + message.user.name
            "
            class="w-8 h-8 rounded-full"
        />
        <div>
            <div class="text-sm font-semibold text-white">
                {{ message.user.name }}
            </div>
            <div
                class="bg-gray-800 text-white rounded px-3 py-2 mt-1 max-w-md break-words"
            >
                {{ message.content }}
            </div>
            <div class="text-xs text-gray-400 mt-1">
                {{ new Date(message.created_at).toLocaleTimeString() }}
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: Object,
    currentUser: Object,
});
</script>
