<script setup>
import ConnectionStatus from '@/components/ConnectionStatus.vue';
import FileUpload from '@/components/FileUpload.vue';
import MentionInput from '@/components/MentionInput.vue';
import RoomMembers from '@/components/RoomMembers.vue';
import TypingIndicator from '@/components/TypingIndicator.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Users } from 'lucide-vue-next';
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

// Props
const props = defineProps({
    user: Object,
    initialRoom: Object,
});

// estado reativo
const rooms = ref([]);
const selectedRoom = ref(props.initialRoom || null);
const messages = ref([]);
const newMessage = ref('');
const newRoomName = ref('');
const isPrivate = ref(false);
const isConnected = ref(true);
const isTyping = ref(false);
const searchQuery = ref('');
const attachedFiles = ref([]);
const friends = ref([]);

// controle de WebSocket
const echoChannels = ref(new Map());
const currentChannel = ref(null);

// ref para o container de mensagens
const messagesContainer = ref(null);

// estado para controlar se deve mostrar o btn de scroll
const showScrollButton = ref(false);

// computed
const filteredRooms = computed(() => {
    if (!searchQuery.value) return rooms.value;
    return rooms.value.filter((room) => room.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const unreadCount = computed(() => {
    return rooms.value.reduce((total, room) => total + (room.unread_count || 0), 0);
});

// Watcher para debug
watch(
    selectedRoom,
    (newRoom, oldRoom) => {
        console.log('selectedRoom mudou de:', oldRoom, 'para:', newRoom);
    },
    { deep: true },
);

// carregar salas
async function loadRooms() {
    try {
        const { data } = await axios.get('/api/rooms');
        rooms.value = data;
        console.log('Salas carregadas:', data);
    } catch (error) {
        console.error('Erro ao carregar salas:', error);
    }
}

// Carregar amigos
async function loadFriends() {
    try {
        const { data } = await axios.get('/api/friends');
        friends.value = data.friends || [];
    } catch (error) {
        console.error('Erro ao carregar amigos:', error);
    }
}

async function loadMessages(roomId, page = 1, append = false) {
    if (!roomId || typeof roomId !== 'number') {
        console.warn('⚠️ loadMessages chamado com roomId inválido:', roomId);
        return;
    }

    try {
        const { data } = await axios.get(`/api/rooms/${roomId}/messages`, {
            params: { page, per_page: 50 },
        });

        if (append) {
            // Adicionar mensagens antigas no início
            messages.value = [...data.messages.reverse(), ...messages.value];
        } else {
            // Carregar mensagens mais recentes
            messages.value = data.messages.reverse();
            // Fazer scroll para o final após carregar as mensagens
            scrollToBottom();
        }

        console.log('Mensagens carregadas para sala', roomId, ':', data);
    } catch (error) {
        console.error('Erro ao carregar mensagens:', error);
    }
}

async function createRoom() {
    if (!newRoomName.value.trim()) return;

    try {
        const { data } = await axios.post('/rooms', {
            name: newRoomName.value,
            private: isPrivate.value,
        });

        rooms.value.push(data.room);
        newRoomName.value = '';
        isPrivate.value = false;
    } catch (error) {
        console.error('Erro ao criar sala:', error);
    }
}

async function sendMessage() {
    if (!newMessage.value.trim() || !selectedRoom.value?.id) return;

    const messageText = newMessage.value.trim();
    newMessage.value = ''; // Limpar o input imediatamente para melhor UX

    try {
        const { data } = await axios.post(
            `/rooms/${selectedRoom.value.id}/messages`,
            { body: messageText },
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

        // Adicionar a mensagem localmente e também aguardar o broadcast
        if (data.message) {
            const messageExists = messages.value.some((msg) => msg.id === data.message.id);
            if (!messageExists) {
                console.log('✅ Adicionando mensagem localmente:', data.message);
                messages.value.push(data.message);
                scrollToBottom();
            }
        }
        console.log('Mensagem enviada com sucesso!');
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);

        // Lidar com rate limiting
        if (error.response?.status === 429) {
            const retryAfter = error.response.data.retry_after || 60;
            alert(`Muitas mensagens enviadas. Tente novamente em ${retryAfter} segundos.`);
        } else if (error.response?.status === 419) {
            // Erro de CSRF - tentar obter novo token
            console.log('Erro 419 - Token CSRF inválido, tentando obter novo token...');
            try {
                const response = await axios.get('/api/csrf-token');
                const newToken = response.data.csrf_token;
                if (newToken) {
                    console.log('Novo token CSRF obtido:', newToken);
                    // Atualizar o meta tag
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', newToken);
                    }
                    // Tentar enviar a mensagem novamente
                    await sendMessage();
                    return;
                }
            } catch (retryError) {
                console.error('Erro ao obter novo token CSRF:', retryError);
            }
            alert('Erro de autenticação. Por favor, recarregue a página e tente novamente.');
        } else {
            // Restaurar a mensagem no input em caso de erro
            newMessage.value = messageText;
        }
    }
}

function selectRoom(room) {
    console.log('selectRoom chamada com:', room);
    if (!room || !room.id) {
        console.error('Sala inválida:', room);
        return;
    }

    selectedRoom.value = room;
    loadMessages(room.id);

    // Marcar como lida
    if (room.unread_count !== undefined) {
        room.unread_count = 0;
    }

    connectToRoom(room.id);

    console.log('Sala selecionada:', room.name);
}

function handleTyping() {
    isTyping.value = true;
    setTimeout(() => {
        isTyping.value = false;
    }, 2000);
}

function handleMention(user) {
    console.log('Usuário mencionado:', user);
}

function handleFileUpload(files) {
    attachedFiles.value = files;
    console.log('Arquivos anexados:', files);
}

function handleUploadError(error) {
    console.error('Erro no upload:', error);
    alert(error);
}

function scrollToBottom() {
    if (messagesContainer.value) {
        nextTick(() => {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
            showScrollButton.value = false;
        });
    }
}

//mostrar boton scroll
function checkScrollPosition() {
    if (messagesContainer.value) {
        const { scrollTop, scrollHeight, clientHeight } = messagesContainer.value;
        const isNearBottom = scrollHeight - scrollTop - clientHeight < 100;
        showScrollButton.value = !isNearBottom;
    }
}

// montar components lifecycle
onMounted(async () => {
    await loadRooms();
    await loadFriends();

    if (selectedRoom.value?.id) {
        loadMessages(selectedRoom.value.id);
        // cnectar à sala específica se veio de uma notificação
        connectToRoom(selectedRoom.value.id);
    }

    // aguardar um pouco para garantir que o Echo esteja pronto
    setTimeout(() => {
        setupEchoListeners();
    }, 1000);
});

// cleanup ao desmontar o componente
onUnmounted(() => {
    cleanupEchoListeners();
});

// onfigurar os listeners do Echo
function setupEchoListeners() {
    if (!window.Echo) {
        console.warn('echo não está disponível');
        return;
    }

    console.log('🔧 Configurando listeners do Echo para', rooms.value.length, 'salas');
    console.log('🔍 Echo disponível:', window.Echo);
    console.log('🔍 Pusher disponível:', window.Pusher);

    cleanupEchoListeners();

    rooms.value.forEach((room) => {
        if (!room?.id) return;

        const channelName = `rooms.${room.id}`;

        try {
            const channel = window.Echo.channel(channelName);
            console.log('📡 Canal criado:', channel);

            echoChannels.value.set(room.id, channel);

            channel.listen('.message.sent', (event) => {
                if (event?.message?.id) {
                    // se e a sala atual, add a lista de msg
                    if (selectedRoom.value?.id === room.id) {
                        const messageExists = messages.value.some((msg) => msg.id === event.message.id);
                        if (!messageExists) {
                            messages.value.push(event.message);

                            scrollToBottom();
                        } else {
                            console.log('⚠️ Mensagem já existe, ignorando:', event.message.id);
                        }
                    } else {
                        // incrementar contador de msg n lidas p outras salas
                        room.unread_count = (room.unread_count || 0) + 1;
                    }
                } else {
                    console.log(' evento sem mensagem válida:', event);
                }
            });

            channel.subscribed(() => {
                console.log(' conectado ao canal da sala:', room.id);
                isConnected.value = true;
            });

            channel.error((error) => {
                console.error('erro no canal da sala:', room.id, error);
                isConnected.value = false;
            });
        } catch (error) {
            console.error('erro ao configurar canal para sala:', room.id, error);
        }
    });
}

//  limpar listeners do echo
function cleanupEchoListeners() {
    if (!window.Echo) return;

    echoChannels.value.forEach((channel, roomId) => {
        window.Echo.leave(`rooms.${roomId}`);
    });

    echoChannels.value.clear();
    currentChannel.value = null;
}

function connectToRoom(roomId) {
    if (!window.Echo || !roomId) return;

    // cesconectar da sala anterior
    if (currentChannel.value) {
        window.Echo.leave(currentChannel.value);
    }

    // conectar a nova sala
    const channelName = `rooms.${roomId}`;
    currentChannel.value = channelName;
}
const breadcrumbs = [
    {
        title: 'Salas',
        href: '#',
    },
];
</script>

<template>
    <Head title="Salas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-screen bg-base-100">
            <div class="flex w-80 flex-col border-r border-base-300 bg-base-200">
                <div class="border-b border-base-300 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h2>
                            <Users />
                        </h2>
                        <div class="flex items-center gap-2">
                            <ConnectionStatus :is-connected="isConnected" />
                            <div v-if="unreadCount > 0" class="badge badge-sm badge-error">
                                {{ unreadCount }}
                            </div>
                        </div>
                    </div>

                    <div class="form-control mb-4">
                        <input v-model="searchQuery" type="text" placeholder="Buscar salas..." class="input-bordered input input-sm w-full" />
                    </div>

                    <button @click="$refs.createRoomModal.showModal()" class="btn w-full btn-sm btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nova Sala
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div v-if="filteredRooms.length === 0" class="p-4 text-center text-base-content/50">
                        <p class="alert alert-info">Nenhuma sala encontrada</p>
                    </div>

                    <div v-else class="space-y-1 p-2">
                        <div
                            v-for="room in filteredRooms"
                            :key="room.id"
                            @click="selectRoom(room)"
                            class="flex cursor-pointer items-center justify-between rounded-lg p-3 transition-colors"
                            :class="selectedRoom?.id === room.id ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <div v-if="room.private" class="badge badge-xs badge-warning">🔒</div>
                                    <div v-else class="badge badge-xs badge-success">🌐</div>
                                </div>
                                <div>
                                    <p class="font-medium">{{ room.name }}</p>
                                    <p class="text-xs opacity-70">
                                        {{ room.private ? 'Privada' : 'Pública' }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="room.unread_count > 0" class="badge badge-sm badge-error">
                                {{ room.unread_count }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-1 flex-col">
                <div class="border-b border-base-300 bg-base-100 p-4">
                    <div v-if="selectedRoom" class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold">{{ selectedRoom.name }}</h3>
                            <p class="text-sm text-base-content/70">
                                {{ selectedRoom.private ? 'Sala Privada' : 'Sala Pública' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <RoomMembers
                                :room="selectedRoom"
                                :current-user-id="props.user.id"
                                :friends="friends"
                                @members-updated="loadRooms"
                                @invites-updated="loadRooms"
                            />
                            <div class="badge" :class="selectedRoom.private ? 'badge-warning' : 'badge-success'">
                                {{ selectedRoom.private ? '🔒 Privada' : '🌐 Pública' }}
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-base-content/50">
                        <h3 class="text-lg">Selecione uma sala para começar a conversar</h3>
                    </div>
                </div>

                <div ref="messagesContainer" @scroll="checkScrollPosition" class="bg-base-50 relative flex-1 overflow-y-auto p-4">
                    <div v-if="!selectedRoom" class="flex h-full items-center justify-center">
                        <div class="text-center">
                            <div class="mb-4 text-6xl">💬</div>
                            <h3 class="mb-2 text-xl font-semibold text-base-content/70">Bem-vindo ao Chat!</h3>
                            <p class="text-base-content/50">Selecione uma sala à esquerda para começar a conversar</p>
                        </div>
                    </div>

                    <div v-else-if="messages.length === 0" class="flex h-full items-center justify-center">
                        <div class="text-center">
                            <div class="mb-4 text-6xl">💬</div>
                            <h3 class="mb-2 text-xl font-semibold text-base-content/70">Nenhuma mensagem ainda</h3>
                            <p class="text-base-content/50">Seja o primeiro a enviar uma mensagem!</p>
                        </div>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-if="messages.length > 0" class="flex justify-center">
                            <button @click="loadMessages(selectedRoom.id, 1, true)" class="btn btn-outline btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                Carregar mensagens antigas
                            </button>
                        </div>

                        <div
                            v-for="message in messages"
                            :key="message.id"
                            class="flex"
                            :class="message.sender?.id === props.user?.id ? 'justify-end' : 'justify-start'"
                        >
                            <div v-if="message.sender?.id !== props.user?.id" class="flex max-w-[70%] items-end gap-2">
                                <div class="avatar">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs text-primary-content">
                                        <span class="font-bold">
                                            <img src="https://avatar.iran.liara.run/public" />
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="mb-1 text-xs text-base-content/60">
                                        {{ message.sender?.name || 'Usuário Desconhecido' }}
                                    </div>
                                    <div class="rounded-2xl rounded-bl-md bg-base-300 px-4 py-2 shadow-sm">
                                        <p class="text-sm">{{ message.body }}</p>
                                    </div>
                                    <div class="mt-1 text-xs text-base-content/50">
                                        {{ new Date(message.created_at).toLocaleTimeString() }}
                                    </div>
                                </div>
                            </div>

                            <div v-else class="flex max-w-[70%] flex-row-reverse items-end gap-2">
                                <div class="avatar">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs text-primary-content">
                                        <span class="font-bold">
                                            <img src="https://avatar.iran.liara.run/public" />
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <div class="mb-1 text-xs text-base-content/60">Você</div>
                                    <div class="rounded-2xl rounded-br-md bg-primary px-4 py-2 text-primary-content shadow-sm">
                                        <p class="text-sm">{{ message.body }}</p>
                                    </div>
                                    <div class="mt-1 text-xs text-base-content/50">
                                        {{ new Date(message.created_at).toLocaleTimeString() }}
                                    </div>
                                </div>
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

                <div v-if="selectedRoom" class="border-t border-base-300 bg-base-100 p-4">
                    <div class="mb-2">
                        <FileUpload @upload-complete="handleFileUpload" @upload-error="handleUploadError" />
                    </div>

                    <div class="flex gap-2">
                        <div class="flex-1">
                            <MentionInput
                                v-model="newMessage"
                                :room-id="selectedRoom.id"
                                placeholder="Digite sua mensagem... (use @ para mencionar)"
                                @mention="handleMention"
                                @keyup.enter="sendMessage"
                                @input="handleTyping"
                            />
                        </div>
                        <button @click="sendMessage" :disabled="!newMessage.trim() && attachedFiles.length === 0" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <dialog ref="createRoomModal" class="modal">
        <div class="modal-box">
            <h3 class="mb-4 text-lg font-bold">Criar Nova Sala</h3>

            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Nome da sala</span>
                </label>
                <input v-model="newRoomName" type="text" placeholder="Digite o nome da sala..." class="input-bordered input w-full" />
            </div>

            <div class="form-control mb-6">
                <label class="label cursor-pointer">
                    <input type="checkbox" v-model="isPrivate" class="checkbox checkbox-primary" />
                    <span class="label-text ml-2">Sala Privada</span>
                </label>
            </div>

            <div class="modal-action">
                <button @click="$refs.createRoomModal.close()" class="btn btn-ghost">Cancelar</button>
                <button
                    @click="
                        createRoom();
                        $refs.createRoomModal.close();
                    "
                    :disabled="!newRoomName.trim()"
                    class="btn btn-primary"
                >
                    Criar Sala
                </button>
            </div>
        </div>
    </dialog>
</template>
