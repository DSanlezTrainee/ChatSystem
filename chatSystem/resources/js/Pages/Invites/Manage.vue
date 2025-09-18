<template>
    
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
