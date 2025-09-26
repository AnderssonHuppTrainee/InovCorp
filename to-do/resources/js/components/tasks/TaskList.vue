<template>
    <div class="flex flex-col">
        <div>
            <ul class="space-y-2">
                <TaskItem
                    v-for="task in tasks"
                    :key="task.id"
                    :task="task"
                    @complete="$emit('complete', task.id)"
                    @delete="$emit('delete', task.id)"
                />
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Task } from '@/types';
import TaskItem from './TaskItem.vue';

defineProps<{
    tasks: Task[];
}>();
</script>
