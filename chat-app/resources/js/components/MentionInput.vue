<template>
    <div class="relative">
        <div
            ref="inputContainer"
            class="input-bordered input flex min-h-[2.5rem] w-full flex-wrap items-center gap-1 p-2"
            :class="{ 'ring-2 ring-primary': showSuggestions }"
            @click="focusInput"
        >
            <!-- Mensagem com menções -->
            <div class="flex w-full flex-wrap gap-1">
                <span
                    v-for="(part, index) in messageParts"
                    :key="index"
                    :class="part.type === 'mention' ? 'rounded-full bg-primary px-2 py-1 text-sm text-primary-content' : ''"
                >
                    {{ part.text }}
                </span>
            </div>

            <!-- Input invisível para capturar digitação-->
            <input
                ref="hiddenInput"
                v-model="inputValue"
                class="min-w-[100px] flex-1 border-none bg-transparent outline-none"
                :placeholder="messageParts.length === 0 ? placeholder : ''"
                @input="handleInput"
                @keydown="handleKeydown"
                @blur="handleBlur"
            />
        </div>

        <!-- Lista de sugestões -->
        <div
            v-if="showSuggestions && filteredUsers.length > 0"
            class="absolute right-0 bottom-full left-0 z-50 mb-2 max-h-48 overflow-y-auto rounded-lg border border-base-300 bg-base-100 shadow-lg"
        >
            <div
                v-for="(user, index) in filteredUsers"
                :key="user.id"
                class="flex cursor-pointer items-center gap-3 p-3 transition-colors hover:bg-base-200"
                :class="{ 'bg-primary text-primary-content': index === selectedIndex }"
                @click="selectUser(user)"
                @mouseenter="selectedIndex = index"
            >
                <div class="avatar">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm text-primary-content">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                </div>
                <div>
                    <p class="font-medium">{{ user.name }}</p>
                    <p class="text-xs opacity-70">{{ user.email }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { computed, nextTick, onMounted, ref, watch } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface MessagePart {
    type: 'text' | 'mention';
    text: string;
    userId?: number;
}

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
    roomId?: number;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
    mention: [user: User];
}>();

const inputContainer = ref<HTMLElement>();
const hiddenInput = ref<HTMLInputElement>();
const inputValue = ref('');
const showSuggestions = ref(false);
const selectedIndex = ref(0);
const users = ref<User[]>([]);
const currentMention = ref('');

// Computed para processar a mensagem em partes
const messageParts = computed<MessagePart[]>(() => {
    const parts: MessagePart[] = [];
    const text = props.modelValue;
    let lastIndex = 0;

    // Encontrar todas as menções
    const mentionRegex = /@(\w+)/g;
    let match;

    while ((match = mentionRegex.exec(text)) !== null) {
        // Adicionar texto antes da menção
        if (match.index > lastIndex) {
            parts.push({
                type: 'text',
                text: text.slice(lastIndex, match.index),
            });
        }

        // Adicionar a menção
        parts.push({
            type: 'mention',
            text: match[0],
            userId: findUserIdByName(match[1]),
        });

        lastIndex = match.index + match[0].length;
    }

    // Adicionar texto restante
    if (lastIndex < text.length) {
        parts.push({
            type: 'text',
            text: text.slice(lastIndex),
        });
    }

    return parts;
});

// Filtrar usuários baseado na menção atual
const filteredUsers = computed(() => {
    if (!currentMention.value) return users.value.slice(0, 5);

    return users.value
        .filter(
            (user) =>
                user.name.toLowerCase().includes(currentMention.value.toLowerCase()) ||
                user.email.toLowerCase().includes(currentMention.value.toLowerCase()),
        )
        .slice(0, 5);
});

// Encontrar ID do usuário pelo nome
function findUserIdByName(name: string): number | undefined {
    const user = users.value.find((u) => u.name.toLowerCase().includes(name.toLowerCase()));
    return user?.id;
}

// Carregar usuários da sala
async function loadUsers() {
    if (!props.roomId) return;

    try {
        const { data } = await axios.get(`/api/rooms/${props.roomId}/users`);
        users.value = data;
    } catch (error) {
        console.error('Erro ao carregar usuários:', error);
    }
}

// Focar no input
function focusInput() {
    hiddenInput.value?.focus();
}

// Lidar com input
function handleInput() {
    const value = inputValue.value;
    const cursorPos = hiddenInput.value?.selectionStart || 0;

    // Encontrar menção atual
    const beforeCursor = value.slice(0, cursorPos);
    const mentionMatch = beforeCursor.match(/@(\w*)$/);

    if (mentionMatch) {
        currentMention.value = mentionMatch[1];
        showSuggestions.value = true;
        selectedIndex.value = 0;
    } else {
        showSuggestions.value = false;
        currentMention.value = '';
    }

    // Atualizar valor do modelo
    emit('update:modelValue', value);
}

// Lidar com teclas
function handleKeydown(event: KeyboardEvent) {
    if (!showSuggestions.value) return;

    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            selectedIndex.value = Math.min(selectedIndex.value + 1, filteredUsers.value.length - 1);
            break;
        case 'ArrowUp':
            event.preventDefault();
            selectedIndex.value = Math.max(selectedIndex.value - 1, 0);
            break;
        case 'Enter':
        case 'Tab':
            event.preventDefault();
            if (filteredUsers.value[selectedIndex.value]) {
                selectUser(filteredUsers.value[selectedIndex.value]);
            }
            break;
        case 'Escape':
            showSuggestions.value = false;
            break;
    }
}

// Selecionar usuário
function selectUser(user: User) {
    const value = inputValue.value;
    const cursorPos = hiddenInput.value?.selectionStart || 0;

    // Encontrar posição da menção atual
    const beforeCursor = value.slice(0, cursorPos);
    const mentionMatch = beforeCursor.match(/@(\w*)$/);

    if (mentionMatch) {
        const startPos = cursorPos - mentionMatch[0].length;
        const newValue = value.slice(0, startPos) + `@${user.name} ` + value.slice(cursorPos);

        inputValue.value = newValue;
        emit('update:modelValue', newValue);
        emit('mention', user);

        showSuggestions.value = false;
        currentMention.value = '';

        nextTick(() => {
            hiddenInput.value?.focus();
            const newCursorPos = startPos + user.name.length + 2;
            hiddenInput.value?.setSelectionRange(newCursorPos, newCursorPos);
        });
    }
}

// Lidar com blur
function handleBlur() {
    // Delay para permitir clique nas sugestões
    setTimeout(() => {
        showSuggestions.value = false;
    }, 200);
}

// Sincronizar com modelValue
function syncWithModelValue() {
    if (props.modelValue !== inputValue.value) {
        inputValue.value = props.modelValue;
    }
}

onMounted(() => {
    loadUsers();
    syncWithModelValue();
});

// Observar mudanças no modelValue
watch(() => props.modelValue, syncWithModelValue);
</script>
