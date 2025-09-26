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

            <div class="mt-5 flex justify-end gap-2">
                <button
                    v-if="props.task.status !== 'completed'"
                    class="text-green-600 hover:text-green-800"
                    @click="$emit('complete', props.task.id)"
                >
                    <SquareCheck />
                </button>
                <Link
                    :href="routeTasks.edit(props.task.id).url"
                    class="text-yellow-600 hover:text-yellow-800"
                >
                    <SquarePen />
                </Link>

                <button
                    class="text-red-600 hover:text-red-800"
                    @click="$emit('delete', props.task.id)"
                >
                    <Trash2 />
                </button>
            </div>
        </li>
    </div>
</template>

<script setup lang="ts">
import routeTasks from '@/routes/tasks';
import { Task } from '@/types';
import { Link } from '@inertiajs/vue3';
import { SquareCheck, SquarePen, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    task: Task; //array de tasks
}>();
</script>
