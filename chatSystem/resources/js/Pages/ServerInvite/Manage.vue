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
        <div class="flex-1 flex flex-col m">
            <header
                class="bg-gray-900 text-white p-4 border-b border-gray-800 flex items-center justify-between"
            >
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">Manage Server Invites</h1>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-800 text-white">
                <div class="max-w-3xl mx-auto mt-5">
                    <!-- Seção de link atual -->
                    <div class="bg-gray-900 p-6 rounded-lg shadow-lg mb-6">
                        <h2 class="text-xl font-bold mb-4">Current Link</h2>

                        <div v-if="pendingInvite" class="mb-4">
                            <div class="bg-gray-800 p-4 rounded-lg mb-3">
                                <div
                                    class="flex flex-col md:flex-row md:items-center justify-between gap-3"
                                >
                                    <div class="flex-1">
                                        <input
                                            type="text"
                                            :value="pendingInvite.invite_link"
                                            readonly
                                            class="w-full bg-gray-700 text-white border border-gray-600 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            @click="
                                                copyInviteLink(
                                                    pendingInvite.invite_link
                                                )
                                            "
                                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="size-6"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click="generateNewInviteLink"
                                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"
                                            :disabled="isGenerating"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="size-6"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-400 mt-2">
                                    <span
                                        >Criado em:
                                        {{
                                            formatDate(pendingInvite.created_at)
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-gray-400 mb-4">
                            No active invite link at the moment.
                        </div>
                    </div>

                    <!-- Notification -->
                    <div
                        v-if="showCopiedNotification"
                        class="fixed bottom-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-lg"
                    >
                        Link copied to clipboard!
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import Sidebar from "@/Pages/Chat/Sidebar.vue";

const props = defineProps({
    pendingInvite: Object,
    usedInvites: Array,
    rooms: Array,
    users: Array,
    currentUser: Object,
});

const isGenerating = ref(false);
const showCopiedNotification = ref(false);

// Gerar novo link de convite
async function generateNewInviteLink() {
    isGenerating.value = true;

    try {
        // Use axios para fazer uma requisição AJAX
        const response = await axios.post(
            "/server-invite/generate",
            {},
            {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                    "X-Inertia": "false",
                },
            }
        );

        if (response.data && response.data.invite_link) {
            // Atualizar pendingInvite diretamente sem recarregar a página
            props.pendingInvite = {
                token: response.data.token,
                invite_link: response.data.invite_link,
                created_at: new Date().toISOString(),
            };

            // Atualizar a página para refletir as mudanças
            router.reload({ only: ["pendingInvite"] });
        }
    } catch (error) {
        console.error("Erro ao gerar link:", error);
        alert("Erro ao gerar novo link de convite");
    } finally {
        isGenerating.value = false;
    }
}

// Copiar link para a área de transferência
function copyInviteLink(link) {
    console.log("Copiando link:", link);

    try {
        // Método mais robusto para copiar para a área de transferência
        const textarea = document.createElement("textarea");
        textarea.value = link;
        textarea.style.position = "fixed";
        textarea.style.left = "-999999px";
        textarea.style.top = "-999999px";
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();

        const success = document.execCommand("copy");
        document.body.removeChild(textarea);

        if (success) {
            console.log("Link copiado com sucesso!");
            showCopiedNotification.value = true;
            setTimeout(() => {
                showCopiedNotification.value = false;
            }, 3000);
        } else {
            console.error("Falha ao copiar o link");
        }
    } catch (err) {
        console.error("Erro ao copiar link:", err);
        // Fallback para navegadores modernos
        navigator.clipboard
            .writeText(link)
            .then(() => {
                showCopiedNotification.value = true;
                setTimeout(() => {
                    showCopiedNotification.value = false;
                }, 3000);
            })
            .catch((err) => {
                console.error("Erro ao copiar com clipboard API:", err);
            });
    }
}

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
</script>
