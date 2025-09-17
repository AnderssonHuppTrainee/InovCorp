<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

// Props
const props = defineProps({
    user: Object, // usuário autenticado
});

// Tipagens
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

// Estado
const friends = ref<User[]>([]);
const requests = ref<Friendship[]>([]);
const selectedFriend = ref<User | null>(null);

// Carregar lista de amigos
async function loadFriends() {
    try {
        const { data } = await axios.get<User[]>('/friends');
        friends.value = data;
    } catch (error) {
        console.error('Erro ao carregar amigos:', error);
    }
}

// Carregar solicitações de amizade
async function loadRequests() {
    try {
        const { data } = await axios.get<Friendship[]>('/friends/requests');
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
        await axios.post(`/friends/${id}/accept`);
        await loadFriends();
        await loadRequests();
    } catch (error) {
        console.error('Erro ao aceitar amizade:', error);
    }
}

async function rejectRequest(id: number) {
    try {
        await axios.post(`/friends/${id}/reject`);
        await loadRequests();
    } catch (error) {
        console.error('Erro ao rejeitar amizade:', error);
    }
}

async function removeFriend(id: number) {
    try {
        await axios.delete(`/friends/${id}`);
        friends.value = friends.value.filter((f) => f.id !== id);
        if (selectedFriend.value?.id === id) selectedFriend.value = null;
    } catch (error) {
        console.error('Erro ao remover amizade:', error);
    }
}

// Inicialização
onMounted(() => {
    loadFriends();
    loadRequests();
});
</script>

<template>
    <Head title="Amigos" />

    <AppLayout>
        <div class="flex h-screen bg-base-100">
            <!-- Coluna 1: Lista de Amigos -->
            <div class="flex w-72 flex-col border-r border-base-300 bg-base-200">
                <div class="border-b border-base-300 p-4">
                    <h2 class="text-xl font-bold">Meus Amigos</h2>
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
                            <div class="avatar">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-content">
                                    {{ friend.name.charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <p class="font-medium">{{ friend.name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 2: Detalhe do amigo -->
            <div class="flex flex-1 flex-col">
                <div class="border-b border-base-300 p-4">
                    <div v-if="selectedFriend">
                        <h3 class="text-lg font-bold">{{ selectedFriend.name }}</h3>
                        <p class="text-sm text-base-content/70">{{ selectedFriend.email }}</p>
                        <button class="btn mt-3 btn-sm btn-error" @click="removeFriend(selectedFriend.id)">Remover Amigo</button>
                    </div>
                    <div v-else class="text-center text-base-content/50">Selecione um amigo para ver detalhes</div>
                </div>
            </div>

            <!-- Coluna 3: Solicitações -->
            <div class="flex w-80 flex-col border-l border-base-300 bg-base-200">
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
                                <button class="btn btn-xs btn-success" @click="acceptRequest(req.id)">Aceitar</button>
                                <button class="btn btn-xs btn-error" @click="rejectRequest(req.id)">Recusar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
