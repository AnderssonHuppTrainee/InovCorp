<template>
  <div v-if="shouldShowTyping" class="chat chat-start">
    <div class="chat-image avatar">
      <div class="w-8 rounded-full bg-base-300 text-base-content flex items-center justify-center">
        <span class="text-xs font-medium">{{ typingUser.name.charAt(0).toUpperCase() }}</span>
      </div>
    </div>
    <div class="chat-bubble chat-bubble-base-300">
      <div class="flex items-center space-x-2">
        <span class="text-sm text-base-content/70">{{ typingText }}</span>
        <div class="flex space-x-1">
          <div class="w-2 h-2 bg-base-content/50 rounded-full animate-bounce"></div>
          <div class="w-2 h-2 bg-base-content/50 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
          <div class="w-2 h-2 bg-base-content/50 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, withDefaults } from 'vue';

interface User {
  id: number;
  name: string;
  email?: string;
}

const props = withDefaults(defineProps<{
  typingUsers?: User[];
  currentUserId?: number;
}>(), {
  typingUsers: () => [],
  currentUserId: 0
});

// Computed para obter usuários que estão digitando (excluindo o usuário atual)
const otherTypingUsers = computed(() => {
  return props.typingUsers.filter(user => user.id !== props.currentUserId);
});

// Computed para determinar se deve mostrar o indicador
const shouldShowTyping = computed(() => {
  return otherTypingUsers.value.length > 0;
});

// Computed para obter o usuário que está digitando
const typingUser = computed(() => {
  const users = otherTypingUsers.value;
  
  if (users.length === 0) {
    return { id: 0, name: 'Alguém' };
  }
  
  if (users.length === 1) {
    return users[0];
  }
  
  // Múltiplos usuários digitando
  return {
    id: 0,
    name: `${users.length} pessoas`
  };
});

// Computed para o texto do indicador
const typingText = computed(() => {
  const users = otherTypingUsers.value;
  
  if (users.length === 0) {
    return 'Alguém está digitando';
  }
  
  if (users.length === 1) {
    return `${users[0].name} está digitando`;
  }
  
  return `${users.length} pessoas estão digitando`;
});
</script>


