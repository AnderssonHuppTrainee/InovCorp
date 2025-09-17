<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
  <div class="flex items-center space-x-2 p-2">

    <div class="avatar">
      <div class="w-12 h-12 rounded-md bg-white text-primary-content flex items-center justify-center">
        <AppLogoIcon class="w-8 h-8 object-cover" />
      </div>
    </div>

    <div class="flex-1">
      <span class="text-sm font-semibold truncate">ChatBubble</span>
    </div>
  </div>
</template>

