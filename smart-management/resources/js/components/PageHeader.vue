<template>
    <div class="flex items-center justify-between">
        <div class="space-y-2">
            <h1
                class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
            >
                {{ title }}
            </h1>
            <p v-if="description" class="text-sm text-muted-foreground">
                {{ description }}
            </p>
        </div>
        <div v-if="$slots.default" class="flex items-center gap-2">
            <slot />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>
