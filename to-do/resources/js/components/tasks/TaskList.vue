<template>
    <AppLayout>
        <div class="flex flex-col items-center">
            <h2 class="mb-4 text-2xl font-bold">Lista De Tasks</h2>
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
    </AppLayout>
</template>

<script setup lang="ts">
import { Task } from '@/types';
import TaskItem from './TaskItem.vue';

defineProps<{
    tasks: Task[];
}>();
</script>
