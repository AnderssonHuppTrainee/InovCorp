<template>
  <div v-if="isTyping" class="chat chat-start">
    <div class="chat-image avatar">
      <div class="w-8 rounded-full bg-base-300 text-base-content flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
      </div>
    </div>
    <div class="chat-bubble chat-bubble-base-300">
      <div class="flex space-x-1">
        <div class="w-2 h-2 bg-base-content/50 rounded-full animate-bounce"></div>
        <div class="w-2 h-2 bg-base-content/50 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
        <div class="w-2 h-2 bg-base-content/50 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  isTyping: boolean;
}>();
</script>


