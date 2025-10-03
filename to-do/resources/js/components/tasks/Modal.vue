<template>
    <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="w-full max-w-lg rounded-lg bg-white shadow-lg">
            <div class="flex items-center justify-between border-b px-4 py-2">
                <h2 class="text-lg font-semibold">{{ title }}</h2>
                <button
                    @click="$emit('update:modelValue', false)"
                    class="text-gray-500 hover:text-gray-800"
                >
                    <CircleX />
                </button>
            </div>

            <div class="p-4">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { CircleX } from 'lucide-vue-next';
const props = defineProps<{
    modelValue: boolean;
    title?: String;
}>();
</script>
