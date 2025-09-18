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
                    <h1 class="text-xl font-bold">
                        Gerenciar Convites de Sala
                    </h1>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white">
                <div class="max-w-3xl mx-auto">
                    <!-- Tabs para alternar entre convites enviados e recebidos -->
                    <div class="border-b border-gray-700 mb-6">
                        <nav class="flex -mb-px">
                            <button
                                @click="activeTab = 'sent'"
                                class="px-4 py-2 text-base font-medium transition-colors"
                                :class="{
                                    'text-blue-400 border-b-2 border-blue-400':
                                        activeTab === 'sent',
                                    'text-gray-400 hover:text-white':
                                        activeTab !== 'sent',
                                }"
                            >
                                Enviados
                            </button>
                            <button
                                @click="activeTab = 'received'"
                                class="px-4 py-2 text-base font-medium transition-colors"
                                :class="{
                                    'text-blue-400 border-b-2 border-blue-400':
                                        activeTab === 'received',
                                    'text-gray-400 hover:text-white':
                                        activeTab !== 'received',
                                }"
                            >
                                Recebidos
                            </button>
                        </nav>
                    </div>

                    <!-- Convites Enviados -->
                    <div v-if="activeTab === 'sent'" class="space-y-4">
                        <h2 class="text-xl font-bold mb-4">
                            Convites Enviados
                        </h2>

                        <div v-if="sentInvites && sentInvites.length > 0">
                            <div class="bg-gray-900 rounded-lg overflow-hidden">
                                <div class="divide-y divide-gray-800">
                                    <div
                                        v-for="invite in sentInvites"
                                        :key="invite.id"
                                        class="p-4"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div>
                                                <p class="font-medium">
                                                    {{ invite.invitee.name }}
                                                </p>
                                                <p
                                                    class="text-sm text-gray-400"
                                                >
                                                    Sala: {{ invite.room.name }}
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
                                                            'text-yellow-500':
                                                                invite.status ===
                                                                'pending',
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
                                            <div>
                                                <button
                                                    v-if="
                                                        invite.status ===
                                                        'pending'
                                                    "
                                                    @click="
                                                        cancelInvite(invite.id)
                                                    "
                                                    class="text-sm px-3 py-1 bg-gray-700 hover:bg-red-800 rounded text-white"
                                                >
                                                    Cancelar
                                                </button>
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
                            Você não enviou nenhum convite para salas.
                        </div>
                    </div>

                    <!-- Convites Recebidos -->
                    <div v-if="activeTab === 'received'" class="space-y-4">
                        <h2 class="text-xl font-bold mb-4">
                            Convites Recebidos
                        </h2>

                        <div
                            v-if="receivedInvites && receivedInvites.length > 0"
                        >
                            <div class="bg-gray-900 rounded-lg overflow-hidden">
                                <div class="divide-y divide-gray-800">
                                    <div
                                        v-for="invite in receivedInvites"
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
                                                            'text-yellow-500':
                                                                invite.status ===
                                                                'pending',
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
                                            <div class="flex space-x-2">
                                                <button
                                                    v-if="
                                                        invite.status ===
                                                        'pending'
                                                    "
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
                                                    v-if="
                                                        invite.status ===
                                                        'pending'
                                                    "
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
    </div>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import Sidebar from "@/Pages/Chat/Sidebar.vue";

const props = defineProps({
    sentInvites: Array,
    receivedInvites: Array,
    rooms: Array,
    users: Array,
    currentUser: Object,
});

const activeTab = ref("received");

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
                router.reload({ only: ["sentInvites", "receivedInvites"] });
            },
        }
    );
}

// Cancelar convite (mesma coisa que recusar, mas pelo remetente)
function cancelInvite(inviteId) {
    updateInviteStatus(inviteId, "declined");
}
</script>
