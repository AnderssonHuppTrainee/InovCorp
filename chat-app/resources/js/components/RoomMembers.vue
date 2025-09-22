<template>
    <div class="room-members">
        <button @click="showModal = true" class="btn text-white btn-sm btn-primary" v-if="canManage">
            <i class="fa fa-user-plus"></i>
        </button>

        <div v-if="showModal" class="modal-open modal">
            <div class="modal-box max-w-4xl">
                <h3 class="mb-4 text-lg font-bold">Adicionar Membros - {{ room.name }}</h3>

                <div class="tabs-bordered mb-4 tabs">
                    <button @click="activeTab = 'members'" class="tab" :class="{ 'tab-active': activeTab === 'members' }">
                        Membros ({{ members.length }})
                    </button>
                    <button @click="activeTab = 'invites'" class="tab" :class="{ 'tab-active': activeTab === 'invites' }">
                        Convites ({{ invites.length }})
                    </button>
                    <button @click="activeTab = 'invite'" class="tab" :class="{ 'tab-active': activeTab === 'invite' }">Convidar</button>
                </div>

                <div v-if="activeTab === 'members'" class="space-y-3">
                    <div v-for="member in members" :key="member.id" class="flex items-center justify-between rounded-lg bg-base-200 p-3">
                        <div class="flex items-center gap-3">
                            <div class="placeholder avatar">
                                <div class="w-8 rounded-full bg-neutral text-neutral-content">
                                    <img :src="member.avatar || 'https://avatar.iran.liara.run/public/boy'" />
                                </div>
                            </div>
                            <div>
                                <div class="font-medium">{{ member.name }}</div>
                                <div class="text-sm text-base-content/70">{{ member.email }}</div>
                            </div>
                            <div v-if="member.id === room.created_by" class="badge badge-primary">Criador</div>
                        </div>
                        <button
                            v-if="canManage && member.id !== room.created_by"
                            @click="removeMember(member)"
                            class="btn btn-outline btn-sm btn-error"
                            :disabled="removing === member.id"
                        >
                            <span v-if="removing === member.id" class="loading loading-xs loading-spinner"></span>
                            Remover
                        </button>
                    </div>
                </div>

                <div v-if="activeTab === 'invites'" class="space-y-3">
                    <div v-for="invite in invites" :key="invite.id" class="flex items-center justify-between rounded-lg bg-base-200 p-3">
                        <div class="flex items-center gap-3">
                            <div class="placeholder avatar">
                                <div class="w-8 rounded-full bg-neutral text-neutral-content">
                                    <span class="text-xs">
                                        <img :src="invite.invited_user?.avatar || 'https://avatar.iran.liara.run/public/boy'" />
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="font-medium">
                                    {{ invite.invited_user?.name || invite.email }}
                                </div>
                                <div class="text-sm text-base-content/70">Convite enviado em {{ formatDate(invite.created_at) }}</div>
                                <div class="text-xs text-base-content/50">Expira em {{ formatDate(invite.expires_at) }}</div>
                            </div>
                            <div class="badge" :class="getStatusBadgeClass(invite.status)">
                                {{ getStatusText(invite.status) }}
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="copyInviteLink(invite)" class="btn btn-outline btn-sm" title="Copiar link do convite">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                    />
                                </svg>
                            </button>
                            <button
                                v-if="invite.status === 'pending'"
                                @click="cancelInvite(invite)"
                                class="btn btn-outline btn-sm btn-error"
                                :disabled="cancelling === invite.id"
                            >
                                <span v-if="cancelling === invite.id" class="loading loading-xs loading-spinner"></span>
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="activeTab === 'invite'" class="space-y-4">
                    <div>
                        <label class="label">
                            <span class="label-text">Convidar amigo</span>
                        </label>
                        <div class="flex gap-2">
                            <select
                                v-model="selectedFriendId"
                                class="select-bordered select flex-1"
                                :disabled="loading || availableFriends.length === 0"
                            >
                                <option value="">
                                    {{ availableFriends.length === 0 ? 'Nenhum amigo disponível' : 'Selecione um amigo' }}
                                </option>
                                <option v-for="friend in availableFriends" :key="friend.id" :value="friend.id">
                                    {{ friend.name }} ({{ friend.email }})
                                </option>
                            </select>
                            <button
                                @click="inviteFriend"
                                class="btn btn-primary"
                                :disabled="!selectedFriendId || loading || availableFriends.length === 0"
                            >
                                <span v-if="loading" class="loading loading-xs loading-spinner"></span>
                                Convidar
                            </button>
                        </div>
                        <div v-if="availableFriends.length === 0" class="mt-2 text-sm text-base-content/70">
                            Todos os seus amigos já são membros desta sala.
                        </div>
                    </div>

                    <div class="divider">OU</div>

                    <div>
                        <label class="label">
                            <span class="label-text">Convidar por email</span>
                        </label>
                        <div class="flex gap-2">
                            <input
                                v-model="inviteEmail"
                                type="email"
                                placeholder="email@exemplo.com"
                                class="input-bordered input flex-1"
                                :disabled="loading"
                            />
                            <button @click="inviteByEmail" class="btn btn-primary" :disabled="!inviteEmail || loading">
                                <span v-if="loading" class="loading loading-xs loading-spinner"></span>
                                Convidar
                            </button>
                        </div>
                    </div>

                    <!-- Link de convite público -->
                    <div class="divider">OU</div>

                    <div>
                        <label class="label">
                            <span class="label-text">Link de convite público</span>
                        </label>
                        <div class="flex gap-2">
                            <input v-model="publicInviteLink" type="text" readonly class="input-bordered input flex-1" />
                            <button @click="copyPublicLink" class="btn btn-outline">
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
                        <div class="mt-1 text-xs text-base-content/70">Qualquer pessoa com este link pode entrar na sala</div>
                    </div>
                </div>

                <!-- Modal actions -->
                <div class="modal-action">
                    <button @click="showModal = false" class="btn btn-outline">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';

interface User {
    id: number;
    avatar: string;
    name: string;
    email: string;
}

interface Room {
    id: number;
    name: string;
    created_by: number;
}

interface Invite {
    id: number;
    status: string;
    email?: string;
    invited_user?: User;
    created_at: string;
    expires_at: string;
    invite_token: string;
}

const props = defineProps<{
    room: Room;
    currentUserId: number;
    friends: User[];
}>();

const emit = defineEmits<{
    'members-updated': [];
    'invites-updated': [];
}>();

const showModal = ref(false);
const activeTab = ref('members');
const members = ref<User[]>([]);
const invites = ref<Invite[]>([]);
const loading = ref(false);
const removing = ref<number | null>(null);
const cancelling = ref<number | null>(null);

// Formulário de convite
const selectedFriendId = ref('');
const inviteEmail = ref('');
const publicInviteLink = ref('');

// Computed
const canManage = computed(() => props.room.created_by === props.currentUserId);

const availableFriends = computed(() => {
    const memberIds = members.value.map((m) => m.id);
    return props.friends.filter((friend) => !memberIds.includes(friend.id));
});

// Métodos
async function loadMembers() {
    try {
        const { data } = await axios.get(`/api/rooms/${props.room.id}/users`);
        members.value = data;
    } catch (error) {
        console.error('Erro ao carregar membros:', error);
    }
}

async function loadInvites() {
    try {
        const { data } = await axios.get(`/api/rooms/${props.room.id}/invites`);
        invites.value = data;
    } catch (error) {
        console.error('Erro ao carregar convites:', error);
    }
}

async function removeMember(member: User) {
    if (!confirm(`Tem certeza que deseja remover ${member.name} da sala?`)) {
        return;
    }

    removing.value = member.id;
    try {
        await axios.delete(`/rooms/${props.room.id}/remove-user/${member.id}`);
        await loadMembers();
        emit('members-updated');
    } catch (error) {
        console.error('Erro ao remover membro:', error);
        alert('Erro ao remover membro');
    } finally {
        removing.value = null;
    }
}

async function inviteFriend() {
    if (!selectedFriendId.value) return;

    loading.value = true;
    try {
        const response = await axios.post(`/rooms/${props.room.id}/invite`, {
            user_id: selectedFriendId.value,
        });

        selectedFriendId.value = '';
        await loadInvites();
        emit('invites-updated');
        alert('Convite enviado com sucesso!');
    } catch (error) {
        console.error('Erro ao enviar convite:', error);

        // Mostrar mensagem de erro específica
        let errorMessage = 'Erro ao enviar convite';
        if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }

        alert(errorMessage);
    } finally {
        loading.value = false;
    }
}

async function inviteByEmail() {
    if (!inviteEmail.value) return;

    loading.value = true;
    try {
        const { data } = await axios.post(`/rooms/${props.room.id}/invite`, {
            email: inviteEmail.value,
        });

        inviteEmail.value = '';
        await loadInvites();
        emit('invites-updated');
        alert('Convite enviado com sucesso!');

        // Mostrar link do convite se disponível
        if (data.invite_url) {
            publicInviteLink.value = data.invite_url;
        }
    } catch (error) {
        console.error('Erro ao enviar convite:', error);
        alert('Erro ao enviar convite');
    } finally {
        loading.value = false;
    }
}

async function cancelInvite(invite: Invite) {
    if (!confirm('Tem certeza que deseja cancelar este convite?')) {
        return;
    }

    cancelling.value = invite.id;
    try {
        await axios.delete(`/rooms/${props.room.id}/invites/${invite.id}`);
        await loadInvites();
        emit('invites-updated');
    } catch (error) {
        console.error('Erro ao cancelar convite:', error);
        alert('Erro ao cancelar convite');
    } finally {
        cancelling.value = null;
    }
}

function copyInviteLink(invite: Invite) {
    const link = `${window.location.origin}/room/invite/${invite.invite_token}/accept`;
    navigator.clipboard.writeText(link);
    alert('Link copiado para a área de transferência!');
}

function copyPublicLink() {
    if (publicInviteLink.value) {
        navigator.clipboard.writeText(publicInviteLink.value);
        alert('Link copiado para a área de transferência!');
    }
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('pt-BR');
}

function getStatusText(status: string) {
    const statusMap = {
        pending: 'Pendente',
        accepted: 'Aceito',
        rejected: 'Rejeitado',
        expired: 'Expirado',
    };
    return statusMap[status] || status;
}

function getStatusBadgeClass(status: string) {
    const classMap = {
        pending: 'badge-warning',
        accepted: 'badge-success',
        rejected: 'badge-error',
        expired: 'badge-neutral',
    };
    return classMap[status] || 'badge-neutral';
}

// Watchers
watch(showModal, (newValue) => {
    if (newValue) {
        loadMembers();
        loadInvites();
        // Gerar link público
        publicInviteLink.value = `${window.location.origin}/room/invite/public/${props.room.id}`;
    }
});

// Watcher para atualizar amigos disponíveis quando membros mudam
watch(
    members,
    () => {
        // Força a reatualização da computed property
    },
    { deep: true },
);

// Lifecycle
onMounted(() => {
    if (showModal.value) {
        loadMembers();
        loadInvites();
    }
});
</script>
