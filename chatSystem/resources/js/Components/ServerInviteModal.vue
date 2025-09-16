<template>
    <div>
        <button @click="openModal" class="settings-btn">
            <svg
                width="32"
                height="32"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
            >
                <circle cx="12" cy="12" r="3" />
                <path
                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09A1.65 1.65 0 0 0 9 4.6V4a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"
                />
            </svg>
        </button>

        <!-- Modal -->
        <div v-if="showModal" class="modal-overlay">
            <div class="modal-content">
                <button class="close-btn" @click="closeModal">&times;</button>
                <div class="server-info">
                    <img :src="serverImage" alt="Server" class="server-img" />
                    <h2>{{ serverName }}</h2>
                </div>
                <div class="invite-section">
                    <label>Invite Link:</label>
                    <input
                        type="text"
                        :value="inviteLink"
                        readonly
                        class="invite-link"
                    />
                    <button @click="copyLink" class="copy-btn">Copy</button>
                </div>
                <div
                    style="
                        display: flex;
                        gap: 12px;
                        justify-content: center;
                        margin-bottom: 8px;
                    "
                >
                    <button
                        @click="generateNewLink"
                        class="copy-btn"
                        style="background: #2563eb"
                    >
                        Gerar novo link
                    </button>
                </div>
                <div v-if="copied" class="copied-msg">Link copied!</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const showModal = ref(false);
const inviteLink = ref("");
const copied = ref(false);

// Customize conforme sua lógica
const serverName = "My server";
const serverImage = "/images/server.png"; // Caminho da imagem do servidor

function openModal() {
    showModal.value = true;
    fetchCurrentInviteLink();
}
function closeModal() {
    showModal.value = false;
    copied.value = false;
}
async function fetchCurrentInviteLink() {
    try {
        const res = await fetch("/server-invite/current", {
            method: "GET",
            headers: {
                Accept: "application/json",
            },
            credentials: "include",
        });
        if (res.ok) {
            const data = await res.json();
            inviteLink.value = data.invite_link || "";
        } else {
            inviteLink.value = "";
        }
    } catch (err) {
        inviteLink.value = "";
    }
}

async function generateNewLink() {
    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        const res = await fetch("/server-invite/generate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
            credentials: "include",
        });
        if (res.ok) {
            const data = await res.json();
            inviteLink.value = data.invite_link;
        } else {
            inviteLink.value = "Error generating link";
        }
    } catch (err) {
        inviteLink.value = "Error generating link";
    }
}
async function copyLink() {
    if (!inviteLink.value || inviteLink.value.startsWith("Error")) {
        copied.value = false;
        return;
    }
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(inviteLink.value);
            copied.value = true;
        } else {
            // Fallback para navegadores antigos
            const textarea = document.createElement("textarea");
            textarea.value = inviteLink.value;
            textarea.style.position = "fixed";
            textarea.style.opacity = "0";
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                document.execCommand("copy");
                copied.value = true;
            } catch (err) {
                copied.value = false;
            }
            document.body.removeChild(textarea);
        }
    } catch (e) {
        copied.value = false;
    }
    setTimeout(() => (copied.value = false), 2000);
}
</script>

<style scoped>
.settings-btn {
    background: none;
    border: none;
    position: absolute;
    bottom: 80px;
    left: 24px;
    cursor: pointer;
    z-index: 20;
}
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}
.modal-content {
    background: #fff;
    border-radius: 12px;
    padding: 32px;
    min-width: 320px;
    max-width: 90vw;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
    position: relative;
}
.close-btn {
    position: absolute;
    top: 12px;
    right: 16px;
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
}
.server-info {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}
.server-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}
.invite-section {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
}
.invite-link {
    flex: 1;
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 1rem;
}
.copy-btn {
    background: #222;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 12px;
    cursor: pointer;
}
.copied-msg {
    color: green;
    font-size: 0.95rem;
    margin-top: 8px;
    text-align: center;
}
</style>
