<template>
   
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import Sidebar from "@/Pages/Chat/Sidebar.vue";

const props = defineProps({
    invite_link: String,
    token: String,
    rooms: Array,
    users: Array,
    currentUser: Object,
});

const isGenerating = ref(false);
const showCopiedNotification = ref(false);

// Gerar novo link de convite
function generateNewLink() {
    isGenerating.value = true;

    router.post(
        "/server-invite/generate",
        {},
        {
            preserveState: true,
            onSuccess: () => {
                // Recarregar a página para mostrar o novo link
                router.reload({ only: ["invite_link", "token"] });
                isGenerating.value = false;
            },
            onError: () => {
                isGenerating.value = false;
            },
        }
    );
}

// Ir para a página de gerenciamento de convites
function goToManageInvites() {
    router.visit("/server-invite/manage");
}

// Copiar link para a área de transferência
function copyInviteLink() {
    if (!props.invite_link) return;

    navigator.clipboard.writeText(props.invite_link).then(() => {
        showCopiedNotification.value = true;
        setTimeout(() => {
            showCopiedNotification.value = false;
        }, 3000);
    });
}
</script>
