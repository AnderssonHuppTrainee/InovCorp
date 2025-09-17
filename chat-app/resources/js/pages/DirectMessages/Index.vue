<script setup lang="ts">
import TypingIndicator from '@/components/TypingIndicator.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

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

interface DirectConversation {
    id: number;
    users: User[];
    last_message?: {
        body: string;
        created_at: string;
    };
    unread_count?: number;
}

interface Message {
    id: number;
    body: string;
    sender: User;
    created_at: string;
}

// Estado
const conversations = ref<DirectConversation[]>([]);
const selectedDm = ref<DirectConversation | null>(null);
const messages = ref<Message[]>([]);
const newMessage = ref('');
const searchQuery = ref('');
const isTyping = ref(false);

// Identificar outro usuário da DM
function getOtherUser(dm: DirectConversation): User | null {
    return dm.users.find((u) => u.id !== props.user.id) || null;
}

// Filtrar conversas
const filteredDMs = computed(() => {
    if (!searchQuery.value) return conversations.value;
    return conversations.value.filter((dm) => {
        const other = getOtherUser(dm);
        return other?.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

// Carregar conversas do usuário
onMounted(async () => {
    try {
        const { data } = await axios.get<DirectConversation[]>('/api/dm');
        conversations.value = data;
    } catch (error) {
        console.error('Erro ao carregar conversas diretas:', error);
    }
});

// Carregar mensagens
async function loadMessages(conversationId: number) {
    try {
        const { data } = await axios.get<Message[]>(`/api/dm/${conversationId}/messages`);
        messages.value = data;
    } catch (error) {
        console.error('Erro ao carregar mensagens:', error);
    }
}

function selectDm(dm: DirectConversation) {
    selectedDm.value = dm;
    loadMessages(dm.id);

    if (dm.unread_count !== undefined) {
        dm.unread_count = 0;
    }
}

// Enviar mensagem
async function sendMessage() {
    if (!newMessage.value.trim() || !selectedDm.value) return;

    try {
        const { data } = await axios.post(
            `api/dm/${selectedDm.value.id}/messages`,
            { body: newMessage.value },
            {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                withCredentials: true,
            },
        );

        messages.value.push(data.message);
        newMessage.value = '';
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);
    }
}

function handleTyping() {
    isTyping.value = true;
    setTimeout(() => (isTyping.value = false), 2000);
}
</script>

<template>
    <Head title="Mensagens Diretas" />

    <AppLayout>
        <div class="flex h-screen bg-base-100">
            <!-- Coluna 1: Lista de DMs -->
            <div class="flex w-80 flex-col border-r border-base-300 bg-base-200">
                <div class="border-b border-base-300 p-4">
                    <h2 class="mb-4 text-xl font-bold text-base-content">Mensagens Diretas</h2>

                    <!-- Busca -->
                    <div class="form-control mb-4">
                        <input v-model="searchQuery" type="text" placeholder="Buscar..." class="input-bordered input input-sm w-full" />
                    </div>
                </div>

                <!-- Lista de DMs -->
                <div class="flex-1 overflow-y-auto">
                    <div v-if="filteredDMs.length === 0" class="p-4 text-center text-base-content/50">
                        <p>Nenhuma conversa encontrada</p>
                    </div>

                    <div v-else class="space-y-1 p-2">
                        <div
                            v-for="dm in filteredDMs"
                            :key="dm.id"
                            @click="selectDm(dm)"
                            class="flex cursor-pointer items-center justify-between rounded-lg p-3 transition-colors"
                            :class="selectedDm?.id === dm.id ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                        >
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-content">
                                        <span class="font-bold">
                                            {{ getOtherUser(dm)?.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-medium">{{ getOtherUser(dm)?.name }}</p>
                                    <p class="max-w-[150px] truncate text-xs opacity-70">
                                        {{ dm.last_message?.body || 'Sem mensagens' }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="dm.unread_count > 0" class="badge badge-sm badge-error">
                                {{ dm.unread_count }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 2: Chat -->
            <div class="flex flex-1 flex-col">
                <!-- Header -->
                <div class="border-b border-base-300 bg-base-100 p-4">
                    <div v-if="selectedDm" class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-content">
                                <span class="font-bold">
                                    {{ getOtherUser(selectedDm)?.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold">{{ getOtherUser(selectedDm)?.name }}</h3>
                    </div>
                    <div v-else class="text-center text-base-content/50">
                        <h3 class="text-lg">Selecione uma conversa para começar</h3>
                    </div>
                </div>

                <!-- Mensagens -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="!selectedDm" class="flex h-full items-center justify-center text-base-content/50">
                        <p>Escolha uma conversa ao lado</p>
                    </div>

                    <div v-else-if="messages.length === 0" class="flex h-full items-center justify-center text-base-content/50">
                        <p>Nenhuma mensagem ainda</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="msg in messages" :key="msg.id" class="chat" :class="msg.sender.id === props.user.id ? 'chat-end' : 'chat-start'">
                            <div class="chat-header">
                                <span class="font-semibold">{{ msg.sender.name }}</span>
                                <time class="text-xs opacity-50">{{ new Date(msg.created_at).toLocaleTimeString() }}</time>
                            </div>
                            <div class="chat-bubble" :class="msg.sender.id === props.user.id ? 'chat-bubble-primary' : 'chat-bubble-base-300'">
                                {{ msg.body }}
                            </div>
                        </div>

                        <TypingIndicator :is-typing="isTyping" />
                    </div>
                </div>

                <!-- Input -->
                <div v-if="selectedDm" class="border-t border-base-300 bg-base-100 p-4">
                    <div class="flex gap-2">
                        <input
                            v-model="newMessage"
                            @keyup.enter="sendMessage"
                            @input="handleTyping"
                            placeholder="Digite sua mensagem..."
                            class="input-bordered input flex-1"
                        />
                        <button @click="sendMessage" :disabled="!newMessage.trim()" class="btn btn-primary">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
