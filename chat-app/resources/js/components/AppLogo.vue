<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex items-center space-x-2 p-2">
        <div class="avatar">
            <div class="flex h-16 w-16 items-center justify-center rounded-md bg-white text-primary-content">
                <AppLogoIcon class="h-full w-full object-cover" />
            </div>
        </div>

        <div class="flex-1">
            <span class="truncate text-lg font-semibold">ChatBubble</span>
        </div>
    </div>
</template>
