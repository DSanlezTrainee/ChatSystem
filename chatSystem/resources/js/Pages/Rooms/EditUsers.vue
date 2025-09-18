<template>
    <div class="flex h-screen bg-black">
        <!-- Sidebar com navegação -->
        <Sidebar
            :rooms="rooms"
            :users="users"
            :selectedRoom="room"
            :selectedUser="null"
            :currentUser="currentUser"
            :jetstream="$page.props.jetstream"
        />

        <!-- Conteúdo principal -->
        <div class="flex-1 flex flex-col">
            <header
                class="bg-gray-900 text-white p-4 border-b border-gray-800 flex items-center justify-between"
            >
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">
                        {{ room.name }}
                    </h1>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white">
                <div class="max-w-3xl mx-auto">
                    <!-- Formulário de edição de usuários -->
                    <form
                        @submit.prevent="updateRoom"
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
                                Users in Room
                            </h2>

                            <!-- Opção para selecionar todos -->
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

                            <!-- Lista de usuários -->
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
                                                        user.avatar ||
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
                                    No users available
                                </div>
                            </div>
                            <div v-if="errors.users" class="text-red-500 mt-1">
                                {{ errors.users }}
                            </div>
                        </div>

                        <!-- Botões do formulário -->
                        <div class="flex items-center justify-between mt-8">
                            <button
                                type="button"
                                @click="cancelEdit"
                                class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :disabled="processing"
                            >
                                <span v-if="processing">Saving...</span>
                                <span v-else>Save Changes</span>
                            </button>
                        </div>
                        <button
                            type="button"
                            @click.prevent="deleteRoom"
                            class="p-2 rounded-full bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 mt-8 mx-auto flex justify-center items-center"
                            title="Excluir Sala"
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { router } from "@inertiajs/vue3";
import Sidebar from "@/Pages/Chat/Sidebar.vue";

const props = defineProps({
    room: Object,
    rooms: Array,
    users: Array,
    currentUser: Object,
    selectedUserIds: Array,
    errors: Object,
});

// Estado do formulário
const form = ref({
    name: "",
    users: [],
});

const processing = ref(false);
const selectAll = ref(false);
const errors = ref({});

// Inicializar formulário com valores da sala
onMounted(() => {
    form.value.name = props.room?.name || "";
    form.value.users = [...(props.selectedUserIds || [])];
    // IDs dos usuários exceto admin
    const nonAdminUserIds = props.users
        ? props.users
              .filter((u) => u.id !== props.currentUser.id)
              .map((u) => u.id)
        : [];
    if (
        nonAdminUserIds.length > 0 &&
        nonAdminUserIds.every((id) => form.value.users.includes(id))
    ) {
        selectAll.value = true;
    } else {
        selectAll.value = false;
    }
});

// Watch to maintain the 'Select All' button state (excluding admin)
watch(
    () => form.value.users,
    (newVal) => {
        const nonAdminUserIds = props.users
            ? props.users
                  .filter((u) => u.id !== props.currentUser.id)
                  .map((u) => u.id)
            : [];
        if (
            nonAdminUserIds.length > 0 &&
            nonAdminUserIds.every((id) => newVal.includes(id))
        ) {
            selectAll.value = true;
        } else {
            selectAll.value = false;
        }
    },
    { deep: true }
);

// Verifica se um usuário está selecionado
const isSelected = (userId) => {
    return form.value.users.includes(userId);
};

// Alterna a seleção de um usuário
const toggleUser = (userId) => {
    if (isSelected(userId)) {
        form.value.users = form.value.users.filter((id) => id !== userId);
        selectAll.value = false;
    } else {
        form.value.users.push(userId);
        selectAll.value = form.value.users.length === props.users.length;
    }
};

// Alterna a seleção de todos os usuários
const toggleSelectAll = () => {
    if (selectAll.value) {
        form.value.users = [];
        selectAll.value = false;
    } else {
        form.value.users = props.users.map((user) => user.id);
        selectAll.value = true;
    }
};

// Atualizar sala
const updateRoom = () => {
    processing.value = true;

    // Adicionar o usuário atual à lista se ainda não estiver
    if (!form.value.users.includes(props.currentUser.id)) {
        form.value.users.push(props.currentUser.id);
    }

    router.put(`/rooms/${props.room.id}`, form.value, {
        onSuccess: () => {
            // Redirecionar para a sala após sucesso
            router.visit(`/rooms/${props.room.id}`);
        },
        onError: (e) => {
            errors.value = e;
            processing.value = false;
        },
    });
};

// Excluir sala
const deleteRoom = () => {
    if (
        confirm(`Tem certeza que deseja excluir a sala "${props.room.name}"?`)
    ) {
        // Usar o mesmo padrão que funciona no Create.vue, sem callbacks complexos e sem redirecionamento manual
        router.delete(
            `/rooms/${props.room.id}`,
            {},
            {
                preserveState: false,
                preserveScroll: false,
                // Não usar onSuccess ou onFinish callbacks
            }
        );
    }
};

// Cancelar edição e voltar para a sala
const cancelEdit = () => {
    router.visit(`/rooms/${props.room.id}`);
};
</script>
