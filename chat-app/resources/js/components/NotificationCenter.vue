<template>
    <div class="relative">
        <!-- Botão de notificações -->
        <button @click="toggleNotifications" class="btn relative btn-circle btn-ghost" :class="{ 'btn-primary': hasUnreadNotifications }">
            <Bell class="h-7 w-7" />
            <!-- Badge de notificações não lidas -->
            <div v-if="unreadCount > 0" class="absolute -top-1 -right-1 badge h-5 min-w-[1.25rem] badge-sm text-xs badge-error">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </div>
        </button>

        <!-- Dropdown de notificações -->
        <div
            v-if="showNotifications"
            class="absolute top-full right-0 z-50 mt-2 max-h-150 w-80 overflow-hidden rounded-lg border border-base-300 bg-base-100 shadow-lg"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-base-300 p-4">
                <h3 class="font-semibold text-base-content">Notificações</h3>
                <div class="flex gap-2">
                    <button v-if="unreadCount > 0" @click="markAllAsRead" class="btn btn-ghost btn-xs">Marcar todas como lidas</button>
                    <button @click="showNotifications = false" class="btn btn-ghost btn-xs">✕</button>
                </div>
            </div>

            <!-- Lista de notificações -->
            <div class="max-h-80 overflow-y-auto">
                <div v-if="notifications.length === 0" class="p-4 text-center text-base-content/50">Nenhuma notificação</div>

                <div v-else class="divide-y divide-base-300">
                    <div
                        v-for="notification in notifications"
                        :key="notification.id"
                        class="cursor-pointer p-4 transition-colors hover:bg-base-200"
                        :class="{ 'bg-primary/10': !notification.read }"
                        @click="handleNotificationClick(notification)"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Avatar do remetente -->
                            <div class="avatar">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full">
                                    <img src="https://avatar.iran.liara.run/public/boy" />
                                </div>
                            </div>

                            <!-- Conteúdo da notificação -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium">{{ notification.title }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-base-content/50">
                                            {{ formatTime(notification.created_at) }}
                                        </span>
                                        <button v-if="!notification.read" @click.stop="markAsRead(notification)" class="btn btn-ghost btn-xs">
                                            ✓
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-base-content/70">{{ notification.message }}</p>

                                <!-- Dados extras -->
                                <div v-if="notification.data?.room_name" class="mt-2">
                                    <span class="badge badge-outline badge-sm">
                                        {{ notification.data.room_name }}
                                    </span>
                                </div>

                                <!-- Ações para convites de sala -->
                                <div v-if="notification.type === 'room_invite' && notification.data?.invite_token" class="mt-2 flex gap-2">
                                    <button @click.stop="acceptRoomInvite(notification)" class="btn btn-xs btn-primary">Aceitar</button>
                                    <button @click.stop="rejectRoomInvite(notification)" class="btn btn-outline btn-xs">Rejeitar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div v-if="notifications.length > 0" class="border-t border-base-300 p-4">
                <button @click="viewAllNotifications" class="btn w-full btn-outline btn-sm">Ver todas as notificações</button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { Bell } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

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
    };
}

const showNotifications = ref(false);
const notifications = ref<Notification[]>([]);
const unreadCount = ref(0);

const hasUnreadNotifications = computed(() => unreadCount.value > 0);

async function loadNotifications() {
    try {
        const { data } = await axios.get('/api/notifications', {
            params: { per_page: 10 },
        });
        notifications.value = data.notifications;
    } catch (error) {
        console.error('Erro ao carregar notificações:', error);
    }
}

async function loadUnreadCount() {
    try {
        const { data } = await axios.get('/api/notifications/unread-count');
        unreadCount.value = data.count;
    } catch (error) {
        console.error('Erro ao carregar contador:', error);
    }
}

function toggleNotifications() {
    showNotifications.value = !showNotifications.value;
    if (showNotifications.value) {
        loadNotifications();
    }
}

async function markAsRead(notification: Notification) {
    try {
        await axios.post(`/api/notifications/${notification.id}/mark-as-read`);
        notification.read = true;
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
        console.error('Erro ao marcar como lida:', error);
    }
}

async function markAllAsRead() {
    try {
        await axios.post('/api/notifications/mark-all-as-read');
        notifications.value.forEach((n) => (n.read = true));
        unreadCount.value = 0;
    } catch (error) {
        console.error('Erro ao marcar todas como lidas:', error);
    }
}

function handleNotificationClick(notification: Notification) {
    if (!notification.read) {
        markAsRead(notification);
    }

    if (notification.data?.room_id) {
        window.location.href = `/rooms/${notification.data.room_id}`;
    }

    showNotifications.value = false;
}

function viewAllNotifications() {
    router.visit('/notifications');
    showNotifications.value = false;
}

async function acceptRoomInvite(notification: Notification) {
    try {
        const response = await axios.post(`/room/invite/${notification.data.invite_token}/accept`);

        // Marcar  como lida
        await markAsRead(notification);

        // Atualizar lista de
        await loadNotifications();

        // redireciona para a sala se a URL foi fornecida
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

async function rejectRoomInvite(notification: Notification) {
    try {
        await axios.post(`/room/invite/${notification.data.invite_token}/reject`);

        await markAsRead(notification);

        await loadNotifications();

        alert('Convite rejeitado');
    } catch (error) {
        console.error('Erro ao rejeitar convite:', error);
        alert('Erro ao rejeitar convite');
    }
}

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

    return date.toLocaleDateString();
}

function handleClickOutside(event: Event) {
    const target = event.target as HTMLElement;
    if (!target.closest('.relative')) {
        showNotifications.value = false;
    }
}

onMounted(() => {
    loadUnreadCount();
    document.addEventListener('click', handleClickOutside);

    const interval = setInterval(loadUnreadCount, 30000);

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
        clearInterval(interval);
    });
});
</script>
