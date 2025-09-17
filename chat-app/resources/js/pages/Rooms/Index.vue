<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface Room {
    id: number;
    name: string;
    private: boolean;
}

const rooms = ref<Room[]>([]);
const newRoomName = ref('');
const isPrivate = ref(false);

// Carregar salas existentes
onMounted(async () => {
    try {
        const { data } = await axios.get<Room[]>('/api/rooms');
        rooms.value = data;
    } catch (error) {
        console.error('Erro ao carregar salas:', error);
    }
});

// Criar nova sala
async function createRoom() {
    if (!newRoomName.value.trim()) return;

    try {
        const { data } = await axios.post('/rooms', {
            name: newRoomName.value,
            private: isPrivate.value,
        });

        // Adiciona a sala criada na lista sem recarregar
        rooms.value.push(data.room);
        newRoomName.value = '';
        isPrivate.value = false;
    } catch (error) {
        console.error(error);
    }
}
</script>

<template>
    <Head title="Salas" />

    <AppLayout>
        <div class="min-h-screen bg-base-200">
            <div class="container mx-auto max-w-4xl p-6">
                <!-- Título da página -->
                <div class="mb-8 text-center">
                    <h2 class="mb-2 text-3xl font-bold text-base-content">Salas de Chat</h2>
                    <p class="text-base-content/70">Conecte-se e converse com outros usuários</p>
                </div>

                <!-- Lista de salas -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="mb-4 card-title text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                            Salas Disponíveis
                        </h3>

                        <!-- Estado vazio -->
                        <div v-if="rooms.length === 0" class="py-12 text-center">
                            <div class="mb-4 text-6xl">🏠</div>
                            <h4 class="mb-2 text-xl font-semibold text-base-content/70">Nenhuma sala criada ainda</h4>
                            <p class="text-base-content/50">Seja o primeiro a criar uma sala de chat!</p>
                        </div>

                        <!-- Grid de salas -->
                        <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="room in rooms"
                                :key="room.id"
                                class="card bg-base-200 shadow-md transition-shadow duration-200 hover:shadow-lg"
                            >
                                <div class="card-body p-4">
                                    <div class="mb-2 flex items-center justify-between">
                                        <h4 class="card-title text-lg">{{ room.name }}</h4>
                                        <div v-if="room.private" class="badge badge-warning">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mr-1 h-3 w-3"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                                />
                                            </svg>
                                            Privada
                                        </div>
                                        <div v-else class="badge badge-success">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mr-1 h-3 w-3"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"
                                                />
                                            </svg>
                                            Pública
                                        </div>
                                    </div>

                                    <p class="mb-4 text-sm text-base-content/70">
                                        {{ room.private ? 'Sala privada - apenas membros convidados' : 'Sala pública - todos podem entrar' }}
                                    </p>

                                    <div class="card-actions justify-end">
                                        <Link :href="`/rooms/${room.id}/chat`" class="btn btn-sm btn-primary">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mr-1 h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                                />
                                            </svg>
                                            Entrar
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
