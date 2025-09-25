<template>
    <div class="">
        <li
            class="rounded bg-white p-4 shadow"
            :class="{
                'line-through opacity-60': props.task.status === 'completed',
            }"
        >
            <h3 class="font-semibold">
                {{ props.task.title }}
                -
                <span class="badge">{{ props.task.priority }}</span>
            </h3>

            <p class="text-sm text-gray-500">{{ props.task.description }}</p>

            <div class="flex gap-2">
                <button
                    v-if="props.task.status !== 'completed'"
                    class="text-green-600 hover:text-green-800"
                    @click="$emit('complete')"
                >
                    Completo
                </button>

                <button
                    class="text-red-600 hover:text-red-800"
                    @click="$emit('delete')"
                >
                    Deletar
                </button>
            </div>
        </li>
    </div>
</template>

<script setup lang="ts">
import { Task } from '@/types';
const props = defineProps<{
    task: Task; //array de tasks
}>();
</script>
