<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-white">{{ title }}</h2>

            <div class="mt-4 bg-gray-900 rounded-md p-4">
                <!-- Everyone Toggle -->
                <div
                    class="flex items-center justify-between mb-3 pb-3 border-b border-gray-700"
                >
                    <div class="flex items-center">
                        <div
                            class="flex-shrink-0 h-8 w-8 bg-gray-700 rounded-full flex items-center justify-center"
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
                            <p class="text-sm font-medium text-white">
                                Everyone
                            </p>
                        </div>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-700 transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="{ 'bg-blue-600': selectAll }"
                            @click="toggleSelectAll"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="{
                                    'translate-x-5': selectAll,
                                    'translate-x-0': !selectAll,
                                }"
                            ></span>
                        </button>
                    </div>
                </div>

                <!-- User List -->
                <div v-if="users.length > 0" class="space-y-3">
                    <div
                        v-for="user in users"
                        :key="user.id"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center">
                            <div
                                v-if="user.profile_photo_url"
                                class="flex-shrink-0 h-8 w-8"
                            >
                                <img
                                    class="h-8 w-8 rounded-full"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                />
                            </div>
                            <div
                                v-else
                                class="flex-shrink-0 h-8 w-8 bg-gray-700 rounded-full flex items-center justify-center"
                            >
                                <span class="text-white text-sm font-medium">{{
                                    user.name.charAt(0)
                                }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">
                                    {{ user.name }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <button
                                type="button"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="{
                                    'bg-blue-600': isSelected(user.id),
                                    'bg-gray-700': !isSelected(user.id),
                                }"
                                @click="toggleUser(user.id)"
                            >
                                <span
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="{
                                        'translate-x-5': isSelected(user.id),
                                        'translate-x-0': !isSelected(user.id),
                                    }"
                                ></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="text-gray-400 text-center py-4">
                    No users available
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="close" class="mr-2"
                    >Cancel</SecondaryButton
                >
                <PrimaryButton @click="confirm">Confirm</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import Modal from "./Modal.vue";
import PrimaryButton from "./PrimaryButton.vue";
import SecondaryButton from "./SecondaryButton.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    users: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: "Select Users",
    },
    preSelectedUserIds: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close", "update", "select-users"]);

const selectAll = ref(false);
const selectedUserIds = ref([...props.preSelectedUserIds]);

// Reset selections when modal is opened
watch(
    () => props.show,
    (newVal) => {
        if (newVal) {
            selectedUserIds.value = [...props.preSelectedUserIds];
            selectAll.value =
                props.preSelectedUserIds.length === props.users.length;
        }
    }
);

const isSelected = (userId) => {
    return selectedUserIds.value.includes(userId);
};

const toggleUser = (userId) => {
    if (isSelected(userId)) {
        selectedUserIds.value = selectedUserIds.value.filter(
            (id) => id !== userId
        );
        selectAll.value = false;
    } else {
        selectedUserIds.value.push(userId);
        selectAll.value = selectedUserIds.value.length === props.users.length;
    }
};

const toggleSelectAll = () => {
    selectAll.value = !selectAll.value;
    if (selectAll.value) {
        // Select all users
        selectedUserIds.value = props.users.map((user) => user.id);
    } else {
        // Deselect all users
        selectedUserIds.value = [];
    }
};

const close = () => {
    emit("close");
};

const confirm = () => {
    emit("select-users", selectedUserIds.value);
    close();
};
</script>
