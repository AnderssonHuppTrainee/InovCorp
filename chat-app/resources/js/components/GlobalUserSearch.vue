<template>
    <div class="global-user-search">
        <button @click="showModal = true" class="btn text-white btn-sm btn-success">
            <i class="fa fa-plus"></i>
        </button>

        <div v-if="showModal" class="modal-open modal">
            <div class="modal-box max-w-2xl">
                <h3 class="mb-4 text-lg font-bold">Buscar e Convidar Usuários</h3>

                <div class="mb-4">
                    <label class="label">
                        <span class="label-text">Buscar por nome, email ou @handle</span>
                    </label>
                    <div class="mt-2 flex gap-2">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Digite nome, email ou @handle..."
                            class="input-bordered input flex-1"
                            @input="debouncedSearch"
                        />
                        <button @click="searchUsers" class="btn btn-primary" :disabled="searchQuery.length < 2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div v-if="searchResults.length > 0" class="mb-4">
                    <h4 class="mb-2 font-semibold">Resultados da busca:</h4>
                    <div class="max-h-60 space-y-2 overflow-y-auto">
                        <div v-for="user in searchResults" :key="user.id" class="flex items-center justify-between rounded-lg bg-base-200 p-3">
                            <div class="flex items-center gap-3">
                                <div class="placeholder avatar">
                                    <div class="w-10 rounded-full bg-neutral text-neutral-content">
                                        <span class="text-sm">
                                            <img src="https://avatar.iran.liara.run/public" />
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-medium">{{ user.name }}</div>
                                    <div class="text-sm text-base-content/70">{{ user.email }}</div>
                                    <div v-if="user.handle" class="text-xs text-primary">@{{ user.handle }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div v-if="user.has_sent_request" class="badge badge-warning">Solicitação Enviada</div>
                                <div v-else-if="user.has_pending_request" class="badge badge-info">Solicitação Recebida</div>
                                <button v-else @click="inviteUser(user)" class="btn btn-sm btn-primary" :disabled="inviting === user.id">
                                    <span v-if="inviting === user.id" class="loading loading-xs loading-spinner"></span>
                                    Convidar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="searchQuery.length >= 2 && !searching" class="mb-4 text-center text-base-content/50">
                    <div class="mb-2 text-4xl">🔍</div>
                    <p>Nenhum usuário encontrado</p>
                </div>

                <div class="divider">OU</div>

                <div class="mb-4">
                    <label class="label">
                        <span class="label-text">Convidar por email</span>
                    </label>
                    <div class="mt-2 flex gap-2">
                        <input
                            v-model="inviteEmail"
                            type="email"
                            placeholder="email@exemplo.com"
                            class="input-bordered input flex-1"
                            @keyup.enter="inviteByEmail"
                        />
                        <button @click="inviteByEmail" class="btn btn-primary" :disabled="!inviteEmail || invitingEmail">
                            <span v-if="invitingEmail" class="loading loading-xs loading-spinner"></span>
                            Convidar
                        </button>
                    </div>
                </div>

                <div class="divider">OU</div>

                <div class="mb-4">
                    <label class="label">
                        <span class="label-text">Seu link de convite de amizade</span>
                    </label>
                    <div class="mt-2 flex gap-2">
                        <input v-model="publicInviteLink" type="text" readonly class="input-bordered input flex-1 border" />
                        <button @click="copyPublicLink" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-1 text-xs text-base-content/70">Compartilhe este link para que outros possam se tornar seus amigos</div>
                </div>

                <div class="modal-action">
                    <button @click="showModal = false" class="btn btn-ghost">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    handle?: string;
    avatar?: string;
    has_sent_request?: boolean;
    has_pending_request?: boolean;
}

const emit = defineEmits<{
    'friend-invited': [];
}>();

const showModal = ref(false);
const searchQuery = ref('');
const searchResults = ref<User[]>([]);
const searching = ref(false);
const inviting = ref<number | null>(null);
const invitingHandle = ref(false);
const invitingEmail = ref(false);

const specificHandle = ref('');
const inviteEmail = ref('');
const publicInviteLink = ref('');

let searchTimeout: number | null = null;

async function searchUsers() {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }

    searching.value = true;
    try {
        const { data } = await axios.get('/api/users/search', {
            params: { q: searchQuery.value },
        });
        searchResults.value = data;
    } catch (error) {
        console.error('Erro ao buscar usuários:', error);
        searchResults.value = [];
    } finally {
        searching.value = false;
    }
}

function debouncedSearch() {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        searchUsers();
    }, 300);
}

async function inviteUser(user: User) {
    inviting.value = user.id;
    try {
        const { data } = await axios.post(`/api/friends/${user.id}/send-request`);

        // Atualiza status do usuário
        user.has_sent_request = true;

        emit('friend-invited');
        alert('Solicitação de amizade enviada com sucesso!');
    } catch (error) {
        console.error('Erro ao enviar solicitação:', error);
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        } else {
            alert('Erro ao enviar solicitação');
        }
    } finally {
        inviting.value = null;
    }
}

async function inviteByHandle() {
    if (!specificHandle.value) return;

    invitingHandle.value = true;
    try {
        const { data } = await axios.post('/api/users/invite-by-handle', {
            handle: specificHandle.value.replace('@', ''),
        });

        specificHandle.value = '';
        emit('friend-invited');
        alert('Solicitação de amizade enviada com sucesso!');
    } catch (error) {
        console.error('Erro ao enviar solicitação:', error);
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        } else {
            alert('Erro ao enviar solicitação');
        }
    } finally {
        invitingHandle.value = false;
    }
}

async function inviteByEmail() {
    if (!inviteEmail.value) return;

    invitingEmail.value = true;
    try {
        const { data: users } = await axios.get('/api/users/search', {
            params: { q: inviteEmail.value },
        });

        if (users.length === 0) {
            alert('Usuário não encontrado com este email.');
            return;
        }

        const user = users[0];
        await inviteUser(user);
        inviteEmail.value = '';
    } catch (error) {
        console.error('Erro ao enviar solicitação:', error);
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        } else {
            alert('Erro ao enviar solicitação');
        }
    } finally {
        invitingEmail.value = false;
    }
}

function copyPublicLink() {
    if (publicInviteLink.value) {
        navigator.clipboard.writeText(publicInviteLink.value);
        alert('Link copiado para a área de transferência!');
    }
}

// Lifecycle
onMounted(() => {
    // Gerar link público de convite
    publicInviteLink.value = `${window.location.origin}/friend/invite/public/${Date.now()}`;
});
</script>
