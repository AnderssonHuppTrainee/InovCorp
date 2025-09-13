<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { usePage, router } from '@inertiajs/vue3'

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
})

// Mensagens da sala
const messages = ref([])
// Nova mensagem do usuário
const newMessage = ref('')

// Carregar mensagens da sala
async function loadMessages() {
  const { data } = await axios.get(`/api/rooms/${props.room.id}/messages`)
  messages.value = data
}

// Enviar mensagem usando axios com configuração específica para Herd
async function sendMessage() {
  if (!newMessage.value.trim()) return

  try {
    // Obter o token CSRF
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!token) {
      console.error('Token CSRF não encontrado');
      return;
    }

    console.log('Enviando mensagem com token CSRF:', token);

    const {data} = await axios.post(`/rooms/${props.room.id}/messages`, {
      body: newMessage.value,
    }, {
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      withCredentials: true
    })
    
    console.log('Mensagem enviada com sucesso:', data);
    // Não adicionamos a mensagem aqui pois ela será recebida via broadcast
    newMessage.value = ''
  } catch (error) {
    console.error('Erro ao enviar mensagem:', error)
    console.error('Detalhes do erro:', error.response?.data)
    
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
  loadMessages()

  // Escutar em tempo real com Laravel Echo
  if (window.Echo) {
    console.log('Echo disponível, conectando ao canal:', `rooms.${props.room.id}`)
    
    const channel = window.Echo.channel(`rooms.${props.room.id}`)
    
    // Tentar diferentes nomes de evento
    channel.listen('message.sent', (event) => {
      console.log("Evento recebido (sem ponto):", event)
      console.log("Mensagem recebida:", event.message)
      // Verificar se a mensagem já existe para evitar duplicação
      const messageExists = messages.value.some(msg => msg.id === event.message.id)
      if (!messageExists) {
        console.log("Adicionando nova mensagem:", event.message)
        messages.value.push(event.message)
      } else {
        console.log("Mensagem já existe, ignorando")
      }
    })
    
    channel.listen('.message.sent', (event) => {
      console.log("Evento recebido (com ponto):", event)
      console.log("Mensagem recebida:", event.message)
      // Verificar se a mensagem já existe para evitar duplicação
      const messageExists = messages.value.some(msg => msg.id === event.message.id)
      if (!messageExists) {
        console.log("Adicionando nova mensagem:", event.message)
        messages.value.push(event.message)
      } else {
        console.log("Mensagem já existe, ignorando")
      }
    })
    
    // Log de conexão
    channel.subscribed(() => {
      console.log('Conectado ao canal:', `rooms.${props.room.id}`)
    })
    
    channel.error((error) => {
      console.error('Erro no canal:', error)
    })
  } else {
    console.error('Echo não está disponível!')
  }
})
</script>

<template>
  <div class="flex flex-col h-screen">
    <!-- Cabeçalho da sala -->
    <div class="p-4 bg-gray-800 text-white font-bold">
      Sala: {{ props.room.name }}
    </div>

    <!-- Lista de mensagens -->
    <div class="flex-1 overflow-y-auto p-4 bg-gray-100">
      <div v-for="message in messages" :key="message.id" class="mb-2">
        <span class="font-bold">{{ message.sender.name }}:</span>
        <span>{{ message.body }}</span>
      </div>
    </div>

    <!-- Input de mensagem -->
    <div class="p-4 bg-white border-t flex">
      <input
        v-model="newMessage"
        @keyup.enter="sendMessage"
        placeholder="Digite sua mensagem..."
        class="flex-1 border rounded p-2 mr-2"
      />
      <button
        @click="sendMessage"
        class="bg-blue-500 text-white px-4 py-2 rounded"
      >
        Enviar
      </button>
    </div>
  </div>
</template>
