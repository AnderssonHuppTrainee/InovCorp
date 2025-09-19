<template>
    <div class="flex items-center gap-2">
        <!-- Avatar -->
        <div class="avatar relative">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm text-primary-content">
                <img src="https://avatar.iran.liara.run/public/boy" class="object-cover" />
            </div>

            <!-- Indicador de status online -->
            <div
                v-if="isOnline"
                class="absolute -right-0.5 -bottom-0.5 h-3 w-3 rounded-full border-2 border-base-100 bg-green-500"
                :title="'Online'"
            ></div>

            <!-- Indicador de status offline -->
            <div v-else class="absolute -right-0.5 -bottom-0.5 h-3 w-3 rounded-full border-2 border-base-100 bg-gray-400" :title="'Offline'"></div>
        </div>

        <!-- Nome do usuário -->
        <div class="flex flex-col">
            <span class="text-sm font-medium">{{ user.name }}</span>
            <span v-if="showLastSeen && !isOnline" class="text-xs text-base-content/50">
                {{ lastSeenText }}
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { computed, onMounted, onUnmounted, ref } from 'vue';

interface User {
    id: number;
    name: string;
    email?: string;
}

const props = defineProps<{
    user: User;
    showLastSeen?: boolean;
}>();

const isOnline = ref(false);
const lastSeen = ref<Date | null>(null);

// Verificar status online
async function checkOnlineStatus() {
    try {
        const { data } = await axios.get(`/api/users/${props.user.id}/status`);
        isOnline.value = data.is_online;
        lastSeen.value = data.last_seen ? new Date(data.last_seen) : null;
    } catch (error) {
        console.error('Erro ao verificar status:', error);
    }
}

// Formatar última vez visto
const lastSeenText = computed(() => {
    if (!lastSeen.value) return 'Nunca visto';

    const now = new Date();
    const diff = now.getTime() - lastSeen.value.getTime();

    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Agora mesmo';
    if (minutes < 60) return `${minutes}m atrás`;
    if (hours < 24) return `${hours}h atrás`;
    if (days < 7) return `${days}d atrás`;

    return lastSeen.value.toLocaleDateString();
});

// Atualizar status periodicamente
let interval: number;

onMounted(() => {
    checkOnlineStatus();

    // Atualizar a cada 30 segundos
    interval = setInterval(checkOnlineStatus, 30000);
});

onUnmounted(() => {
    if (interval) {
        clearInterval(interval);
    }
});
</script>
