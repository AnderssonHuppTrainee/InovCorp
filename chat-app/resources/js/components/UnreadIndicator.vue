<template>
  <div v-if="count > 0" class="relative">
    <div class="badge badge-error badge-sm">
      {{ count > 99 ? '99+' : count }}
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  count: number;
}>();
</script>

