<template>
    <div class="flex h-screen bg-black">
        <!-- Sidebar com navegação -->
        <Sidebar
            :rooms="rooms"
            :users="users"
            :selectedRoom="null"
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
                    <h1 class="text-xl font-bold">Convites de Sala</h1>
                </div>
                <div>
                    <button
                        @click="goToManage"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"
                    >
                        Gerenciar Convites
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-xl font-bold mb-6">Convites Recebidos</h2>

                    <div v-if="invites && invites.length > 0">
                        <div class="bg-gray-900 rounded-lg overflow-hidden">
                            <div class="divide-y divide-gray-800">
                                <div
                                    v-for="invite in pendingInvites"
                                    :key="invite.id"
                                    class="p-4"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <p class="font-medium">
                                                {{ invite.room.name }}
                                            </p>
                                            <p class="text-sm text-gray-400">
                                                De: {{ invite.inviter.name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{
                                                    formatDate(
                                                        invite.created_at
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button
                                                @click="
                                                    updateInviteStatus(
                                                        invite.id,
                                                        'accepted'
                                                    )
                                                "
                                                class="text-sm px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-white"
                                            >
                                                Aceitar
                                            </button>
                                            <button
                                                @click="
                                                    updateInviteStatus(
                                                        invite.id,
                                                        'declined'
                                                    )
                                                "
                                                class="text-sm px-3 py-1 bg-gray-700 hover:bg-red-800 rounded text-white"
                                            >
                                                Recusar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="pendingInvites.length === 0"
                            class="bg-gray-900 p-6 rounded-lg text-gray-400 text-center mt-4"
                        >
                            Não há convites pendentes.
                        </div>

                        <div v-if="historicInvites.length > 0" class="mt-8">
                            <h3
                                class="text-lg font-semibold mb-4 text-gray-300"
                            >
                                Histórico de Convites
                            </h3>

                            <div class="bg-gray-900 rounded-lg overflow-hidden">
                                <div class="divide-y divide-gray-800">
                                    <div
                                        v-for="invite in historicInvites"
                                        :key="invite.id"
                                        class="p-4"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div>
                                                <p class="font-medium">
                                                    {{ invite.room.name }}
                                                </p>
                                                <p
                                                    class="text-sm text-gray-400"
                                                >
                                                    De:
                                                    {{ invite.inviter.name }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        formatDate(
                                                            invite.created_at
                                                        )
                                                    }}
                                                    ·
                                                    <span
                                                        :class="{
                                                            'text-green-500':
                                                                invite.status ===
                                                                'accepted',
                                                            'text-red-500':
                                                                invite.status ===
                                                                'declined',
                                                        }"
                                                    >
                                                        {{
                                                            statusLabel(
                                                                invite.status
                                                            )
                                                        }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="bg-gray-900 p-6 rounded-lg text-gray-400 text-center"
                    >
                        Você não recebeu nenhum convite para salas.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Sidebar from "@/Pages/Chat/Sidebar.vue";

const props = defineProps({
    invites: Array,
    rooms: Array,
    users: Array,
    currentUser: Object,
});

const pendingInvites = computed(() => {
    return props.invites?.filter((invite) => invite.status === "pending") || [];
});

const historicInvites = computed(() => {
    return props.invites?.filter((invite) => invite.status !== "pending") || [];
});

// Formatar data
function formatDate(dateString) {
    if (!dateString) return "";
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }).format(date);
}

// Obter label do status
function statusLabel(status) {
    const labels = {
        pending: "Pendente",
        accepted: "Aceito",
        declined: "Recusado",
    };
    return labels[status] || status;
}

// Navegar para página de gerenciamento
function goToManage() {
    router.visit("/invites/manage");
}

// Atualizar status do convite
function updateInviteStatus(inviteId, newStatus) {
    router.patch(
        `/invites/${inviteId}`,
        {
            status: newStatus,
        },
        {
            preserveState: true,
            onSuccess: () => {
                router.reload({ only: ["invites"] });
            },
        }
    );
}
</script>
