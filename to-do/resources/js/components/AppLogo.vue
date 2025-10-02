<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="bg-sidebar-white flex aspect-square size-10 items-center justify-center rounded-md text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-10 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-lg">
        <span class="mb-0.5 truncate leading-tight font-semibold"
            >TaskFlow</span
        >
    </div>
</template>
