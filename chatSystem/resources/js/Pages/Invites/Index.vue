<template>
   
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
