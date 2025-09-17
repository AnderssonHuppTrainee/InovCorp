<template>
  <div class="flex items-center gap-2 text-sm">
    <div class="flex items-center gap-1">
      <div 
        class="w-2 h-2 rounded-full transition-colors duration-300"
        :class="isConnected ? 'bg-success' : 'bg-error'"
      ></div>
      <span :class="isConnected ? 'text-success' : 'text-error'">
        {{ isConnected ? 'Conectado' : 'Desconectado' }}
      </span>
    </div>
    
    <div v-if="!isConnected" class="flex items-center gap-1 text-warning">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
      </svg>
      <span>Reconectando...</span>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  isConnected: boolean;
}>();
</script>


