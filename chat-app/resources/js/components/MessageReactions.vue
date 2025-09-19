<template>
  <div v-if="reactions.length > 0" class="flex flex-wrap gap-1 mt-2">
    <button
      v-for="reaction in reactions"
      :key="reaction.emoji"
      @click="toggleReaction(reaction.emoji)"
      class="flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-base-200 hover:bg-base-300 transition-colors"
      :class="{ 'bg-primary text-primary-content': hasUserReacted(reaction.emoji) }"
    >
      <span>{{ reaction.emoji }}</span>
      <span>{{ reaction.count }}</span>
    </button>
    
    <!-- Botão para adicionar reação -->
    <button
      @click="showEmojiPicker = !showEmojiPicker"
      class="flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-base-200 hover:bg-base-300 transition-colors"
    >
      <span>😊</span>
    </button>
  </div>

  <!-- Picker de emojis -->
  <div
    v-if="showEmojiPicker"
    class="absolute bottom-full left-0 mb-2 bg-base-100 border border-base-300 rounded-lg shadow-lg p-2 z-50"
  >
    <div class="grid grid-cols-6 gap-1">
      <button
        v-for="emoji in commonEmojis"
        :key="emoji"
        @click="addReaction(emoji)"
        class="w-8 h-8 flex items-center justify-center hover:bg-base-200 rounded transition-colors"
      >
        {{ emoji }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

interface Reaction {
  emoji: string;
  count: number;
  users: any[];
  user_ids: number[];
}

const props = defineProps<{
  messageId: number;
  reactions: Reaction[];
  currentUserId: number;
}>();

const emit = defineEmits<{
  'reaction-updated': [reactions: Reaction[]];
}>();

const showEmojiPicker = ref(false);

// Emojis comuns
const commonEmojis = [
  '👍', '👎', '❤️', '😂', '😮', '😢', '😡', '🎉',
  '👏', '🔥', '💯', '✨', '🚀', '💪', '🙌', '😍',
  '🤔', '😴', '🤯', '🥳', '😎', '🤝', '💡', '🎯'
];

// Verificar se o usuário reagiu com um emoji específico
function hasUserReacted(emoji: string): boolean {
  const reaction = props.reactions.find(r => r.emoji === emoji);
  return reaction ? reaction.user_ids.includes(props.currentUserId) : false;
}

// Alternar reação
async function toggleReaction(emoji: string) {
  try {
    const { data } = await axios.post(`/api/messages/${props.messageId}/reactions`, {
      emoji: emoji
    });
    
    emit('reaction-updated', data.reactions);
    showEmojiPicker.value = false;
  } catch (error) {
    console.error('Erro ao alternar reação:', error);
  }
}

// Adicionar reação
function addReaction(emoji: string) {
  toggleReaction(emoji);
}

// Fechar picker ao clicar fora
function handleClickOutside(event: Event) {
  const target = event.target as HTMLElement;
  if (!target.closest('.relative')) {
    showEmojiPicker.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
