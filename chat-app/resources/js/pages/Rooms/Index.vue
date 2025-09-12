<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { Link } from '@inertiajs/vue3';
import axios from 'axios'

interface Room {
  id: number
  name: string
  private: boolean
}

const rooms = ref<Room[]>([])
const newRoomName = ref('')
const isPrivate = ref(false)

// Carregar salas existentes
onMounted(async () => {
  try {
    const { data } = await axios.get<Room[]>('/api/rooms')
    rooms.value = data
  } catch (error) {
    console.error('Erro ao carregar salas:', error)
  }
})

// Criar nova sala
async function createRoom() {
  if (!newRoomName.value.trim()) return

  try {
    const { data } = await axios.post('/rooms', {
      name: newRoomName.value,
      private: isPrivate.value
    })

    // Adiciona a sala criada na lista sem recarregar
    rooms.value.push(data.room)
    newRoomName.value = ''
    isPrivate.value = false

  } catch (error) {
    console.error(error)
  }
}
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Salas de Chat</h1>

    <!-- Formulário de nova sala -->
    <div class="mb-6 p-4 bg-gray-100 rounded flex items-center gap-2">
      <input
        v-model="newRoomName"
        placeholder="Nome da sala"
        class="border p-2 rounded flex-1"
      />
      <label class="flex items-center gap-1">
        <input type="checkbox" v-model="isPrivate" />
        Privada
      </label>
      <button
        @click="createRoom"
        class="bg-blue-500 text-white p-2 rounded"
      >
        Criar Sala
      </button>
    </div>

    <!-- Lista de salas -->
    <div v-if="rooms.length === 0">
      <p>Nenhuma sala criada</p>
    </div>
    <ul v-else class="space-y-2">
      <li
        v-for="room in rooms"
        :key="room.id"
        class="p-2 bg-white rounded shadow"
      >
         <Link :href="`/rooms/${room.id}`">{{ room.name }}</Link>
      </li>
    </ul>
  </div>
</template>
