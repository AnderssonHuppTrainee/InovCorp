<template>
    <header
        class="flex items-center justify-between rounded bg-gray-600 px-4 py-3 shadow"
    >
        <h1 class="text-xl font-bold text-white">{{ title }}</h1>
        <slot name="actions"> </slot>
    </header>
</template>

<script setup lang="ts">
defineProps<{
    title: String;
}>();
</script>
