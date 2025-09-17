<script setup>
import ConnectionStatus from '@/components/ConnectionStatus.vue';
import TypingIndicator from '@/components/TypingIndicator.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

// Configurar CSRF token para este componente
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}

// Props vindas do Inertia (quando acessar /rooms/{id})
const props = defineProps({
    room: Object, // Dados da sala
    user: Object, // Usuário autenticado
});

// Mensagens da sala
const messages = ref([]);
// Nova mensagem do usuário
const newMessage = ref('');
// Estado da conexão
const isConnected = ref(true);
// Estado de digitação
const isTyping = ref(false);

// Carregar mensagens da sala
async function loadMessages() {
    const { data } = await axios.get(`/api/rooms/${props.room.id}/messages`);
    messages.value = data;
}

// Função para simular digitação (pode ser expandida para funcionalidade real)
function handleTyping() {
    isTyping.value = true;
    // Simular parada de digitação após 2 segundos
    setTimeout(() => {
        isTyping.value = false;
    }, 2000);
}

// Enviar mensagem usando axios com configuração específica para Herd
async function sendMessage() {
    if (!newMessage.value.trim()) return;

    try {
        // Obter o token CSRF
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!token) {
            console.error('Token CSRF não encontrado');
            return;
        }

        console.log('Enviando mensagem com token CSRF:', token);

        const { data } = await axios.post(
            `/rooms/${props.room.id}/messages`,
            {
                body: newMessage.value,
            },
            {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                withCredentials: true,
            },
        );

        console.log('Mensagem enviada com sucesso:', data);
        // Não adicionamos a mensagem aqui pois ela será recebida via broadcast
        newMessage.value = '';
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);
        console.error('Detalhes do erro:', error.response?.data);

        // Se for erro 419, tentar obter um novo token CSRF
        if (error.response?.status === 419) {
            console.log('Erro 419 - tentando obter novo token CSRF...');
            try {
                const response = await axios.get('/');
                const newToken = response.data.match(/name="csrf-token" content="([^"]+)"/)?.[1];
                if (newToken) {
                    console.log('Novo token CSRF obtido:', newToken);
                    // Tentar novamente com o novo token
                    await sendMessage();
                }
            } catch (retryError) {
                console.error('Erro ao obter novo token CSRF:', retryError);
            }
        }
    }
}

// Carregar mensagens quando o componente for montado
onMounted(() => {
    loadMessages();

    // Escutar em tempo real com Laravel Echo
    if (window.Echo) {
        console.log('Echo disponível, conectando ao canal:', `rooms.${props.room.id}`);

        const channel = window.Echo.channel(`rooms.${props.room.id}`);

        // Tentar diferentes nomes de evento
        channel.listen('message.sent', (event) => {
            console.log('Evento recebido (sem ponto):', event);
            console.log('Mensagem recebida:', event.message);
            // Verificar se a mensagem já existe para evitar duplicação
            const messageExists = messages.value.some((msg) => msg.id === event.message.id);
            if (!messageExists) {
                console.log('Adicionando nova mensagem:', event.message);
                messages.value.push(event.message);
            } else {
                console.log('Mensagem já existe, ignorando');
            }
        });

        channel.listen('.message.sent', (event) => {
            console.log('Evento recebido (com ponto):', event);
            console.log('Mensagem recebida:', event.message);
            // Verificar se a mensagem já existe para evitar duplicação
            const messageExists = messages.value.some((msg) => msg.id === event.message.id);
            if (!messageExists) {
                console.log('Adicionando nova mensagem:', event.message);
                messages.value.push(event.message);
            } else {
                console.log('Mensagem já existe, ignorando');
            }
        });

        // Log de conexão
        channel.subscribed(() => {
            console.log('Conectado ao canal:', `rooms.${props.room.id}`);
            isConnected.value = true;
        });

        channel.error((error) => {
            console.error('Erro no canal:', error);
            isConnected.value = false;
        });
    } else {
        console.error('Echo não está disponível!');
    }
});
</script>

<template>
    <div class="min-h-screen bg-base-200">
        <!-- Header da sala -->
        <div class="navbar bg-primary text-primary-content shadow-lg">
            <div class="flex-1">
                <div class="flex items-center gap-3">
                    <Link href="/rooms" class="btn btn-circle btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold">💬 {{ props.room.name }}</h1>
                        <p class="text-sm opacity-70">{{ props.room.private ? 'Sala Privada' : 'Sala Pública' }}</p>
                    </div>
                </div>
            </div>
            <div class="flex-none">
                <ConnectionStatus :is-connected="isConnected" />
            </div>
        </div>

        <!-- Container principal -->
        <div class="flex h-[calc(100vh-64px)]">
            <!-- Área de mensagens -->
            <div class="flex flex-1 flex-col">
                <!-- Lista de mensagens -->
                <div class="flex-1 space-y-4 overflow-y-auto p-4">
                    <!-- Estado vazio -->
                    <div v-if="messages.length === 0" class="flex h-full items-center justify-center">
                        <div class="text-center">
                            <div class="mb-4 text-6xl">💬</div>
                            <h3 class="mb-2 text-xl font-semibold text-base-content/70">Nenhuma mensagem ainda</h3>
                            <p class="text-base-content/50">Seja o primeiro a enviar uma mensagem!</p>
                        </div>
                    </div>

                    <!-- Mensagens -->
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        class="chat"
                        :class="message.sender.id === props.user.id ? 'chat-end' : 'chat-start'"
                    >
                        <div class="avatar chat-image">
                            <div class="flex w-10 items-center justify-center rounded-full bg-primary text-primary-content">
                                <img src="https://avatar.iran.liara.run/public" />
                            </div>
                        </div>
                        <div class="chat-header">
                            <span class="font-semibold">{{ message.sender.name }}</span>
                            <time class="text-xs opacity-50">{{ new Date(message.created_at).toLocaleTimeString() }}</time>
                        </div>
                        <div class="chat-bubble" :class="message.sender.id === props.user.id ? 'chat-bubble-primary' : 'chat-bubble-base-300'">
                            {{ message.body }}
                        </div>
                    </div>

                    <!-- Indicador de digitação -->
                    <TypingIndicator :is-typing="isTyping" />
                </div>

                <!-- Input de mensagem -->
                <div class="border-t bg-base-100 p-4">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <input
                                v-model="newMessage"
                                @keyup.enter="sendMessage"
                                @input="handleTyping"
                                placeholder="Digite sua mensagem..."
                                class="input-bordered input w-full"
                            />
                        </div>
                        <button @click="sendMessage" :disabled="!newMessage.trim()" class="btn btn-primary">
                            Enviar
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar com informações da sala -->
            <div class="w-80 border-l border-base-300 bg-base-100 p-4">
                <div class="card bg-base-200 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Informações da Sala
                        </h3>

                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">Nome:</span>
                                <span>{{ props.room.name }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="font-semibold">Tipo:</span>
                                <div class="badge" :class="props.room.private ? 'badge-warning' : 'badge-success'">
                                    {{ props.room.private ? 'Privada' : 'Pública' }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="font-semibold">Mensagens:</span>
                                <span class="badge badge-outline">{{ messages.length }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usuário atual -->
                <div class="card mt-4 bg-base-200 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                            Você
                        </h3>

                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="flex w-12 items-center justify-center rounded-full bg-primary text-primary-content">
                                    <img src="https://avatar.iran.liara.run/public" />
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold">{{ props.user.name }}</p>
                                <p class="text-sm opacity-70">{{ props.user.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
