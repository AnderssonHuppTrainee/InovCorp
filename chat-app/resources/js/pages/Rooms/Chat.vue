<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Echo from 'laravel-echo'

const route = useRoute()
const roomId = route.params.id
const messages = ref([])
const newMessage = ref('')

// carregar histórico
onMounted(async () => {
  const { data } = await axios.get(`/rooms/${roomId}/messages`)
  messages.value = data

  // configurar Echo
  window.Echo.channel(`room.${roomId}`)
    .listen('MessageSent', e => {
      messages.value.push(e.message)
    })
})

async function sendMessage() {
  const { data } = await axios.post(`/rooms/${roomId}/messages`, { body: newMessage.value })
  newMessage.value = ''
}
</script>

<template>
  <div class="chat">
    <div class="messages">
      <div v-for="msg in messages" :key="msg.id">
        <strong>{{ msg.sender.name }}:</strong> {{ msg.body }}
      </div>
    </div>
    <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Digite sua mensagem" />
  </div>
</template>
