<script setup lang="ts">
import TypingIndicator from '@/components/TypingIndicator.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';

// declaracoes de tipo para Echo e Pusher
declare global {
    interface Window {
        Echo: any;
        Pusher: any;
    }
}

interface DirectMessageEvent {
    message?: {
        id: number;
        direct_conversation_id: number;
        body: string;
        sender_id: number;
        created_at: string;
        updated_at: string;
        sender: {
            id: number;
            name: string;
        };
    };
    [key: string]: any; // permitir outras propriedades
}

const props = defineProps<{
    user?: User;
}>();

// pegar user do Inertia page props
const page = usePage();
const currentUser = props.user || (page.props.auth as any)?.user;

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
    unread_count: number;
}

interface DirectMessage {
    id: number;
    sender: User;
    body: string;
    created_at: string;
    updated_at: string;
}

const conversations = ref<DirectConversation[]>([]);
const selectedDm = ref<DirectConversation | null>(null);
const messages = ref<DirectMessage[]>([]);
const newMessage = ref('');
const searchQuery = ref('');
const isTyping = ref(false);
const currentChannel = ref<string | null>(null);
const friends = ref<User[]>([]);
const showFriendsSearch = ref(false);
const friendsSearchQuery = ref('');

const messagesContainer = ref<HTMLElement | null>(null);

// estado para controlar se deve mostrar o btn de scroll
const showScrollButton = ref(false);

function getOtherUser(dm: DirectConversation): User | null {
    if (!currentUser) return null;
    return dm.users.find((u) => u.id !== currentUser.id) || null;
}

const filteredDMs = computed(() => {
    if (!conversations.value) return [];
    if (!searchQuery.value) return conversations.value;
    return conversations.value.filter((dm) => {
        const other = getOtherUser(dm);
        return other?.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

const filteredFriends = computed(() => {
    if (!friends.value) return [];
    if (!friendsSearchQuery.value) return friends.value;
    return friends.value.filter((friend) => {
        return friend.name.toLowerCase().includes(friendsSearchQuery.value.toLowerCase());
    });
});

async function loadFriends() {
    try {
        const { data } = await axios.get<User[]>('/api/friends');
        friends.value = data;
    } catch (error) {
        console.error('Erro ao carregar amigos:', error);
    }
}

async function startConversationWithFriend(friend: User) {
    try {
        const { data } = await axios.post('/api/dm', {
            user_id: friend.id,
        });

        // add a nova conversa à lista
        conversations.value.push(data.conversation);

        // seleciona a conversa
        selectDm(data.conversation);

        // fecha busca de amigos
        showFriendsSearch.value = false;
        friendsSearchQuery.value = '';

        console.log('Conversa iniciada com:', friend.name);
    } catch (error) {
        console.error('Erro ao iniciar conversa:', error);
    }
}
function scrollToBottom() {
    if (messagesContainer.value) {
        nextTick(() => {
            if (messagesContainer.value) {
                messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
                showScrollButton.value = false;
            }
        });
    }
}
function checkScrollPosition() {
    if (messagesContainer.value) {
        const { scrollTop, scrollHeight, clientHeight } = messagesContainer.value;
        const isNearBottom = scrollHeight - scrollTop - clientHeight < 100;
        showScrollButton.value = !isNearBottom;
    }
}

// Alternar busca de amigos
function toggleFriendsSearch() {
    showFriendsSearch.value = !showFriendsSearch.value;
    if (showFriendsSearch.value) {
        loadFriends();
    } else {
        friendsSearchQuery.value = '';
    }
}

async function loadMessages(conversationId: number) {
    try {
        const { data } = await axios.get<DirectMessage[]>(`/api/dm/${conversationId}/messages`);
        messages.value = data;
        scrollToBottom();
    } catch (error) {
        console.error('Erro ao carregar mensagens:', error);
    }
}

function selectDm(dm: DirectConversation) {
    selectedDm.value = dm;
    loadMessages(dm.id);
    connectToConversation(dm.id);

    if (dm.unread_count !== undefined) {
        dm.unread_count = 0;
    }
}

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
        scrollToBottom();

        // Handle different response structures
        if (Array.isArray(data)) {
            messages.value = data;
        } else if (data && data.messages) {
            messages.value = data.messages;
        } else if (data && data.message) {
            // Adicionar a nova mensagem ao array existente
            const messageExists = messages.value.some((msg) => msg.id === data.message.id);
            if (!messageExists) {
                messages.value.push(data.message);
            }
        } else {
            console.warn('Resposta inesperada da API:', data);
        }
        newMessage.value = '';
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);
    }
}

function handleTyping() {
    isTyping.value = true;
    setTimeout(() => (isTyping.value = false), 2000);
}

// Função para conectar a uma conversa específica
function connectToConversation(conversationId: number) {
    if (!window.Echo || !conversationId) return;

    // Desconectar da conversa anterior
    if (currentChannel.value) {
        console.log('Desconectando do canal:', currentChannel.value);
        window.Echo.leave(currentChannel.value);
    }

    // Conectar à nova conversa
    const channelName = `direct-conversation.${conversationId}`;
    console.log('Conectando ao canal:', channelName);

    try {
        const channel = window.Echo.private(channelName);
        console.log('Canal criado:', channel);

        // Escutar mensagens diretas usando o método 'on' do Echo (que funciona)
        channel.on('direct-message.sent', (e: any) => {
            console.log('📨 Mensagem direta recebida:', e);

            if (e.message && selectedDm.value?.id === conversationId) {
                // Adicionar mensagem apenas se não for do próprio usuário
                if (e.message.sender_id !== currentUser?.id) {
                    const messageExists = messages.value.some((msg) => msg.id === e.message.id);
                    if (!messageExists) {
                        console.log(' Adicionando mensagem em tempo real');
                        messages.value.push(e.message);
                    }
                }
            }
        });

        currentChannel.value = channelName;
        console.log('Conectado ao canal de conversa direta:', channelName);
    } catch (error) {
        console.error(' Erro ao conectar ao canal:', error);
    }
}

// Função para limpar listeners do Echo
function cleanupEchoListeners() {
    if (!window.Echo) return;

    console.log('Limpando listeners do Echo...');

    if (currentChannel.value) {
        console.log('Removendo listener da conversa:', currentChannel.value);
        window.Echo.leave(currentChannel.value);
        currentChannel.value = null;
    }
}

// Configurar listeners quando o componente for montado
onMounted(async () => {
    try {
        const { data } = await axios.get<DirectConversation[]>('/api/dm');
        conversations.value = data;

        // Verificar query string para abrir conversa específica
        const conversationId = Number(new URLSearchParams(window.location.search).get('conversation_id'));
        if (conversationId) {
            const dm = conversations.value.find((c) => c.id === conversationId);
            if (dm) selectDm(dm);
        }

        // Aguardar um pouco para garantir que o Echo esteja pronto
        setTimeout(() => {
            console.log('🔧 Echo disponível:', window.Echo);
        }, 1000);
    } catch (error) {
        console.error('Erro ao carregar conversas diretas:', error);
    }
});

// Limpar recursos quando o componente for desmontado
onUnmounted(() => {
    console.log('Componente sendo desmontado, limpando recursos...');
    cleanupEchoListeners();
});
</script>

<template>
    <Head title="Mensagens Diretas" />

    <AppLayout>
        <div class="flex h-screen bg-base-100">
            <!--list de dms-->
            <div class="flex w-80 flex-col border-r border-base-300 bg-base-200">
                <div class="border-b border-base-300 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-base-content">Mensagens Diretas</h2>
                        <button @click="toggleFriendsSearch" class="btn btn-sm btn-primary" :class="{ 'btn-outline': !showFriendsSearch }">
                            {{ showFriendsSearch ? 'Conversas' : 'Novo Chat' }}
                        </button>
                    </div>

                    <div v-if="!showFriendsSearch" class="form-control mb-4">
                        <input v-model="searchQuery" type="text" placeholder="Buscar conversas..." class="input-bordered input input-sm w-full" />
                    </div>

                    <div v-else class="form-control mb-4">
                        <input v-model="friendsSearchQuery" type="text" placeholder="Buscar amigos..." class="input-bordered input input-sm w-full" />
                    </div>
                </div>

                <div v-if="!showFriendsSearch" class="flex-1 overflow-y-auto">
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
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full">
                                        <img src="https://avatar.iran.liara.run/public/boy" />
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

                <div v-else class="flex-1 overflow-y-auto">
                    <div v-if="filteredFriends.length === 0" class="p-4 text-center text-base-content/50">
                        <p>Nenhum amigo encontrado</p>
                    </div>

                    <div v-else class="space-y-1 p-2">
                        <div
                            v-for="friend in filteredFriends"
                            :key="friend.id"
                            @click="startConversationWithFriend(friend)"
                            class="flex cursor-pointer items-center gap-3 rounded-lg p-3 transition-colors hover:bg-base-300"
                        >
                            <div class="avatar">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full">
                                    <img src="https://avatar.iran.liara.run/public/boy" />
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">{{ friend.name }}</p>
                                <p class="text-xs opacity-70">{{ friend.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--chat-->
            <div class="flex flex-1 flex-col">
                <div class="border-b border-base-300 bg-base-100 p-4">
                    <div v-if="selectedDm" class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full">
                                <img src="https://avatar.iran.liara.run/public/boy" />
                            </div>
                        </div>
                        <h3 class="text-lg font-bold">{{ getOtherUser(selectedDm)?.name }}</h3>
                    </div>
                    <div v-else class="text-center text-base-content/50">
                        <h3 class="text-lg">Selecione uma conversa para começar</h3>
                    </div>
                </div>

                <!-- mensagens -->
                <div ref="messagesContainer" @scroll="checkScrollPosition" class="flex-1 overflow-y-auto p-4">
                    <div v-if="!selectedDm" class="flex h-full items-center justify-center text-base-content/50">
                        <p>Escolha uma conversa ao lado</p>
                    </div>

                    <div v-else-if="!messages || messages.length === 0" class="flex h-full items-center justify-center text-base-content/50">
                        <p>Nenhuma mensagem ainda</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="msg in messages"
                            :key="msg.id"
                            class="chat"
                            :class="msg.sender?.id === currentUser?.id ? 'chat-end' : 'chat-start'"
                        >
                            <div class="chat-header">
                                <span class="font-semibold">{{ msg.sender.name }}</span>
                                <time class="text-xs opacity-50">{{ new Date(msg.created_at).toLocaleTimeString() }}</time>
                            </div>
                            <div class="chat-bubble" :class="msg.sender?.id === currentUser?.id ? 'chat-bubble-primary' : 'chat-bubble-base-300'">
                                {{ msg.body }}
                            </div>
                        </div>

                        <TypingIndicator :is-typing="isTyping" />
                    </div>
                    <div v-if="showScrollButton" class="absolute right-4 bottom-4">
                        <button @click="scrollToBottom" class="btn btn-circle shadow-lg btn-sm btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- input -->
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
