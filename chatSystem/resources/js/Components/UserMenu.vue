<script setup>
import { router } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

defineProps({
    user: Object,
    jetstream: Object,
});

const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <div class="avatar-btn">
        <Dropdown align="top" width="48">
            <template #trigger>
                <button
                    v-if="jetstream.managesProfilePhotos"
                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                >
                    <img
                        class="size-8 rounded-full object-cover"
                        :src="user.profile_photo_url"
                        :alt="user.name"
                    />
                </button>
                <span v-else class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                    >
                        {{ user.name }}
                        <svg
                            class="ms-2 -me-0.5 size-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </button>
                </span>
            </template>
            <template #content>
                <div class="block px-4 py-2 text-xs text-gray-400">
                    Manage Account
                </div>
                <DropdownLink :href="route('profile.show')"
                    >Profile</DropdownLink
                >
                <DropdownLink
                    v-if="jetstream.hasApiFeatures"
                    :href="route('api-tokens.index')"
                    >API Tokens</DropdownLink
                >
                <div class="border-t border-gray-200" />
                <form @submit.prevent="logout">
                    <DropdownLink as="button">Log Out</DropdownLink>
                </form>
            </template>
        </Dropdown>
    </div>
</template>

<style scoped>
.avatar-btn {
    background: none;
    border: none;
    position: absolute;
    left: 200px;
    bottom: 80px;
    cursor: pointer;
    z-index: 20;
}
</style>
