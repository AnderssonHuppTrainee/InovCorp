<script setup lang="ts">
import GlobalUserSearch from '@/components/GlobalUserSearch.vue';
import UserStatus from '@/components/UserStatus.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

// tipagens
interface User {
    id: number;
    name: string;
    email: string;
}

interface Friendship {
    id: number;
    user_id: number;
    friend_id: number;
    status: 'pending' | 'accepted' | 'blocked' | 'rejected';
    user?: User;
    friend?: User;
}
const props = defineProps<{
    user: User;
}>();

// estado
const friends = ref<User[]>([]);
const requests = ref<Friendship[]>([]);
const selectedFriend = ref<User | null>(null);
const searchQuery = ref('');
const searchResults = ref<User[]>([]);
const showSearchResults = ref(false);

async function loadFriends() {
    try {
        const { data } = await axios.get<User[]>('api/friends');
        friends.value = data;
    } catch (error) {
        console.error('Erro ao carregar amigos:', error);
    }
}

async function loadRequests() {
    try {
        const { data } = await axios.get<Friendship[]>('api/friends/requests');
        requests.value = data;
    } catch (error) {
        console.error('Erro ao carregar solicitações:', error);
    }
}

function selectFriend(friend: User) {
    selectedFriend.value = friend;
}

async function acceptRequest(id: number) {
    try {
        await axios.post(`api/friends/${id}/accept`);
        await loadFriends();
        await loadRequests();
    } catch (error) {
        console.error('Erro ao aceitar amizade:', error);
    }
}

async function rejectRequest(id: number) {
    try {
        await axios.post(`api/friends/${id}/reject`);
        await loadRequests();
    } catch (error) {
        console.error('Erro ao rejeitar amizade:', error);
    }
}

async function removeFriend(id: number) {
    try {
        await axios.delete(`api/friends/${id}`);
        friends.value = friends.value.filter((f) => f.id !== id);
        if (selectedFriend.value?.id === id) selectedFriend.value = null;
    } catch (error) {
        console.error('Erro ao remover amizade:', error);
    }
}

async function searchUsers() {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        showSearchResults.value = false;
        return;
    }

    try {
        const { data } = await axios.get('/api/friends/search-users', {
            params: { q: searchQuery.value },
        });
        searchResults.value = data;
        showSearchResults.value = true;
    } catch (error) {
        console.error('Erro ao buscar usuários:', error);
    }
}

async function sendFriendRequest(user: User) {
    try {
        await axios.post(`/api/friends/${user.id}/send-request`);
        searchQuery.value = '';
        searchResults.value = [];
        showSearchResults.value = false;
        await loadRequests();
        alert(`Solicitação enviada para ${user.name}`);
    } catch (error) {
        console.error('Erro ao enviar solicitação:', error);
        alert('Erro ao enviar solicitação de amizade');
    }
}

async function startDirectMessage(friend: User) {
    try {
        // busca todas as conversas
        const { data } = await axios.get('/api/dm');
        let conversation = data.find((conv: any) => conv.users.some((u: any) => u.id === friend.id));

        // se n existe, cria nova
        if (!conversation) {
            const { data: newConversation } = await axios.post('/api/dm', {
                user_id: friend.id,
            });
            conversation = newConversation;
        }

        // redireciona p DirectMessages/Index.vue com query string
        window.location.href = `/dm?conversation_id=${conversation.id}`;
    } catch (error) {
        console.error('Erro ao iniciar conversa:', error);
        alert('Erro ao iniciar conversa');
    }
}

// inicialização
onMounted(() => {
    loadFriends();
    loadRequests();
});
const breadcrumbs = [
    {
        title: 'Amigos',
        href: '#',
    },
];
</script>

<template>
    <Head title="Amigos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-screen bg-base-100">
            <div class="flex w-70 flex-col border-r border-base-300 bg-base-200">
                <div class="border-b border-base-300 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold">Meus Amigos</h2>
                        <GlobalUserSearch @friend-invited="loadFriends" />
                    </div>

                    <div class="relative">
                        <input
                            v-model="searchQuery"
                            @input="searchUsers"
                            type="text"
                            placeholder="Buscar usuários..."
                            class="input-bordered input input-sm w-full"
                        />

                        <div
                            v-if="showSearchResults && searchResults.length > 0"
                            class="absolute top-full right-0 left-0 z-50 mt-1 max-h-48 overflow-y-auto rounded-lg border border-base-300 bg-base-100 shadow-lg"
                        >
                            <div
                                v-for="user in searchResults"
                                :key="user.id"
                                class="flex cursor-pointer items-center gap-3 p-3 hover:bg-base-200"
                                @click="sendFriendRequest(user)"
                            >
                                <div class="avatar">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full">
                                        <img src="https://avatar.iran.liara.run/public/boy" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ user.name }}</p>
                                    <p class="text-xs text-base-content/50">{{ user.email }}</p>
                                </div>
                                <button class="btn btn-xs btn-primary">Adicionar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div v-if="friends.length === 0" class="p-4 text-center text-base-content/50">Nenhum amigo ainda</div>

                    <div v-else class="space-y-1 p-2">
                        <div
                            v-for="friend in friends"
                            :key="friend.id"
                            @click="selectFriend(friend)"
                            class="flex cursor-pointer items-center gap-3 rounded-lg p-2 transition-colors"
                            :class="selectedFriend?.id === friend.id ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                        >
                            <UserStatus :user="friend" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-1 flex-col">
                <div class="border-b border-base-300 p-4">
                    <div v-if="selectedFriend">
                        <UserStatus :user="selectedFriend" :show-last-seen="true" />
                        <div class="whitespace-wrap mt-4 flex gap-2">
                            <button class="btn text-white btn-sm btn-primary" @click="startDirectMessage(selectedFriend)">
                                <i class="fa fa-comment"></i>
                                Iniciar Conversa
                            </button>
                            <button class="btn text-white btn-sm btn-error" @click="removeFriend(selectedFriend.id)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-center text-base-content/50">Selecione um amigo para ver detalhes</div>
                </div>
            </div>

            <div class="flex w-60 flex-col border-l border-base-300 bg-base-200">
                <div class="border-b border-base-300 p-4">
                    <h2 class="text-xl font-bold">Solicitações</h2>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div v-if="requests.length === 0" class="p-4 text-center text-base-content/50">Nenhuma solicitação pendente</div>

                    <div v-else class="space-y-2 p-2">
                        <div v-for="req in requests" :key="req.id" class="flex items-center justify-between rounded-lg bg-base-100 p-3">
                            <div>
                                <p class="font-medium">
                                    {{ req.user?.id === props.user.id ? req.friend?.name : req.user?.name }}
                                </p>
                                <p class="text-xs opacity-70">Pedido de amizade</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="btn text-white btn-xs btn-success" @click="acceptRequest(req.id)">
                                    <i class="fa fa-check"></i>
                                </button>
                                <button class="btn text-white btn-xs btn-error" @click="rejectRequest(req.id)">
                                    <i class="fa fa-cancel"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
