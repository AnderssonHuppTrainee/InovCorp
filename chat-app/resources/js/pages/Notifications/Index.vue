<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Notification {
    id: number;
    type: string;
    title: string;
    message: string;
    data?: any;
    read: boolean;
    created_at: string;
    from_user?: {
        id: number;
        name: string;
        avatar?: string;
    };
}

const props = defineProps<{
    user: User;
}>();

const notifications = ref<Notification[]>([]);
const loading = ref(false);
const currentPage = ref(1);
const hasMore = ref(true);

const unreadCount = computed(() => notifications.value.filter((n) => !n.read).length);

// Carregar notificações
async function loadNotifications(page = 1, append = false) {
    if (loading.value) return;

    loading.value = true;
    try {
        const { data } = await axios.get('/api/notifications', {
            params: {
                page,
                per_page: 20,
            },
        });

        if (append) {
            notifications.value = [...notifications.value, ...data.notifications];
        } else {
            notifications.value = data.notifications;
        }

        hasMore.value = data.pagination.has_more;
        currentPage.value = page;
    } catch (error) {
        console.error('Erro ao carregar notificações:', error);
    } finally {
        loading.value = false;
    }
}

// Carregar mais notificações
async function loadMore() {
    if (hasMore.value && !loading.value) {
        await loadNotifications(currentPage.value + 1, true);
    }
}

// Marcar notificação como lida
async function markAsRead(notification: Notification) {
    if (notification.read) return;

    try {
        await axios.post(`/api/notifications/${notification.id}/mark-as-read`);
        notification.read = true;
    } catch (error) {
        console.error('Erro ao marcar como lida:', error);
    }
}

// Marcar todas como lidas
async function markAllAsRead() {
    try {
        await axios.post('/api/notifications/mark-all-as-read');
        notifications.value.forEach((n) => (n.read = true));
    } catch (error) {
        console.error('Erro ao marcar todas como lidas:', error);
    }
}

// Deletar notificação
async function deleteNotification(notification: Notification) {
    if (!confirm('Tem certeza que deseja deletar esta notificação?')) {
        return;
    }

    try {
        await axios.delete(`/api/notifications/${notification.id}`);
        const index = notifications.value.findIndex((n) => n.id === notification.id);
        if (index > -1) {
            notifications.value.splice(index, 1);
        }
    } catch (error) {
        console.error('Erro ao deletar notificação:', error);
    }
}

// Aceitar convite de sala
async function acceptRoomInvite(notification: Notification) {
    try {
        const response = await axios.post(`/room/invite/${notification.data.invite_token}/accept`);

        // Marcar notificação como lida
        await markAsRead(notification);

        // Atualizar lista de notificações
        await loadNotifications();

        // Redirecionar para a sala se a URL foi fornecida
        if (response.data.redirect_url) {
            window.location.href = response.data.redirect_url;
        } else {
            alert('Convite aceito com sucesso!');
        }
    } catch (error) {
        console.error('Erro ao aceitar convite:', error);
        alert('Erro ao aceitar convite');
    }
}

// Rejeitar convite de sala
async function rejectRoomInvite(notification: Notification) {
    try {
        await axios.post(`/room/invite/${notification.data.invite_token}/reject`);

        // Marcar notificação como lida
        await markAsRead(notification);

        // Atualizar lista de notificações
        await loadNotifications();

        alert('Convite rejeitado.');
    } catch (error) {
        console.error('Erro ao rejeitar convite:', error);
        alert('Erro ao rejeitar convite');
    }
}

// Lidar com clique na notificação
function handleNotificationClick(notification: Notification) {
    if (!notification.read) {
        markAsRead(notification);
    }

    // Navegar baseado no tipo de notificação
    if (notification.data?.room_id) {
        window.location.href = `/rooms/${notification.data.room_id}`;
    }
}

// Formatar tempo
function formatTime(dateString: string) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now.getTime() - date.getTime();

    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Agora';
    if (minutes < 60) return `${minutes}m`;
    if (hours < 24) return `${hours}h`;
    if (days < 7) return `${days}d`;

    return date.toLocaleDateString('pt-BR');
}

// Formatar tipo de notificação
function getTypeIcon(type: string) {
    const icons = {
        room_invite: '🏠',
        mention: '@',
        message: '💬',
        friendship_request: '👤',
        friendship_accepted: '✅',
    };
    return icons[type] || '📢';
}

// Formatar tipo de notificação
function getTypeColor(type: string) {
    const colors = {
        room_invite: 'text-blue-600',
        mention: 'text-yellow-600',
        message: 'text-green-600',
        friendship_request: 'text-purple-600',
        friendship_accepted: 'text-green-600',
    };
    return colors[type] || 'text-gray-600';
}

onMounted(() => {
    loadNotifications();
});

const breadcrumbs = [
    {
        title: 'Notificações',
        href: '#',
    },
];
</script>

<template>
    <Head title="Notificações" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-screen bg-base-100">
            <!-- Conteúdo principal -->
            <div class="flex flex-1 flex-col">
                <!-- Header -->
                <div class="border-b border-base-300 bg-base-100 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-base-content">Notificações</h1>
                            <p class="text-base-content/70">
                                {{ notifications.length }} notificações
                                <span v-if="unreadCount > 0" class="text-primary"> ({{ unreadCount }} não lidas) </span>
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button v-if="unreadCount > 0" @click="markAllAsRead" class="btn btn-outline btn-sm">Marcar todas como lidas</button>
                        </div>
                    </div>
                </div>

                <!-- Lista de notificações -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="notifications.length === 0 && !loading" class="flex h-full items-center justify-center text-base-content/50">
                        <div class="text-center">
                            <div class="mb-4 text-6xl">🔔</div>
                            <p class="text-lg">Nenhuma notificação</p>
                            <p class="text-sm">Você está em dia!</p>
                        </div>
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="notification in notifications"
                            :key="notification.id"
                            class="flex cursor-pointer items-start gap-4 rounded-lg border border-base-300 p-4 transition-colors hover:bg-base-200"
                            :class="{ 'border-primary/20 bg-primary/5': !notification.read }"
                            @click="handleNotificationClick(notification)"
                        >
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-base-200 text-lg">
                                {{ getTypeIcon(notification.type) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-semibold text-base-content">{{ notification.title }}</h3>
                                            <span v-if="!notification.read" class="h-2 w-2 rounded-full bg-primary"></span>
                                        </div>
                                        <p class="mt-1 text-sm text-base-content/70">{{ notification.message }}</p>

                                        <div v-if="notification.data?.room_name" class="mt-2">
                                            <span class="badge badge-outline badge-sm">
                                                {{ notification.data.room_name }}
                                            </span>
                                        </div>

                                        <div
                                            v-if="notification.type === 'room_invite' && notification.data?.invite_token && !notification.read"
                                            class="mt-3 flex gap-2"
                                        >
                                            <button @click.stop="acceptRoomInvite(notification)" class="btn btn-sm btn-primary" :disabled="loading">
                                                <span v-if="loading" class="loading loading-xs loading-spinner"></span>
                                                Aceitar
                                            </button>
                                            <button @click.stop="rejectRoomInvite(notification)" class="btn btn-outline btn-sm" :disabled="loading">
                                                Rejeitar
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Ações -->
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-base-content/50">
                                            {{ formatTime(notification.created_at) }}
                                        </span>
                                        <div class="flex gap-1">
                                            <button
                                                v-if="!notification.read"
                                                @click.stop="markAsRead(notification)"
                                                class="btn text-white btn-sm btn-success"
                                                title="Marcar como lida"
                                            >
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button
                                                @click.stop="deleteNotification(notification)"
                                                class="btn text-white btn-sm btn-error"
                                                title="Deletar"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botão carregar mais -->
                        <div v-if="hasMore" class="flex justify-center pt-4">
                            <button @click="loadMore" :disabled="loading" class="btn btn-outline">
                                <span v-if="loading" class="loading loading-sm loading-spinner"></span>
                                {{ loading ? 'Carregando...' : 'Carregar mais' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
