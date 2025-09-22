<template>
    <div class="relative">
        <div
            v-if="modelValue && roomId"
            class="absolute top-full right-0 z-50 mt-2 max-h-150 w-96 rounded-lg border border-base-300 bg-base-100 shadow-lg"
        >
            <div class="flex items-center justify-between border-b border-base-300 p-4">
                <h3 class="font-semibold text-base-content">Membros da Sala</h3>
                <button @click="$emit('update:modelValue', false)" class="btn btn-ghost btn-xs">✕</button>
            </div>

            <div class="max-h-80 overflow-y-auto">
                <div v-if="members.length === 0" class="p-4 text-center text-base-content/50">Nenhum membro</div>

                <div v-else class="divide-y divide-base-300">
                    <div v-for="member in members" :key="member.id" class="flex items-center justify-between p-3 hover:bg-base-200">
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="h-8 w-8 rounded-full">
                                    <img :src="member.avatar || 'https://avatar.iran.liara.run/public/boy'" />
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ member.name }}</p>
                                <p class="text-xs opacity-70">{{ member.is_admin ? '👑 Admin' : 'Membro' }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2" v-if="isCurrentUserAdmin">
                            <button v-if="!member.is_admin" @click="makeAdmin(member)" class="btn btn-outline btn-xs">Tornar Admin</button>
                            <button @click="removeUser(member)" class="btn text-white btn-xs btn-error">
                                <i class="fa fa-times"></i>
                                Remover
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-base-300 p-4 text-right">
                <button @click="refreshMembers" class="btn btn-outline btn-sm">Atualizar Lista</button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface Member {
    id: number;
    name: string;
    avatar?: string;
    is_admin: boolean;
}

const emit = defineEmits(['update:modelValue']);
const props = defineProps<{
    modelValue: boolean;
    roomId?: number;
    currentUserId: number;
}>();

const members = ref<Member[]>([]);
const isCurrentUserAdmin = ref(false);

async function loadMembers() {
    if (!props.roomId) {
        return;
    }

    try {
        const { data } = await axios.get(`/api/rooms/${props.roomId}/users`);
        members.value = data;
        // Descobrir se o usuário atual é admin
        const currentUser = data.find((m: Member) => m.id === props.currentUserId);
        isCurrentUserAdmin.value = currentUser?.is_admin ?? false;
    } catch (error) {
        console.error('Erro ao carregar membros:', error);
    }
}

async function makeAdmin(member: Member) {
    if (!props.roomId) {
        console.warn('RoomInfo: roomId não fornecido para makeAdmin');
        return;
    }

    try {
        await axios.post(`/api/rooms/${props.roomId}/users/${member.id}/make-admin`);
        member.is_admin = true;
        alert('Usuário promovido a admin com sucesso!');
    } catch (error) {
        console.error('Erro ao promover usuário:', error);
        alert('Erro ao promover usuário');
    }
}

async function removeUser(member: Member) {
    if (!props.roomId) {
        console.warn('RoomInfo: roomId não fornecido para removeUser');
        return;
    }

    try {
        await axios.delete(`/rooms/${props.roomId}/remove-user/${member.id}`);
        members.value = members.value.filter((m) => m.id !== member.id);
    } catch (error) {
        console.error('Erro ao remover usuário:', error);
        alert('Erro ao remover usuário');
    }
}

function refreshMembers() {
    loadMembers();
}

onMounted(loadMembers);
</script>
