<script setup lang="ts">
import TaskForm from '@/components/tasks/TaskForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Task } from '@/types';
import { useFlashMessages } from '@/composables/useFlashMessages';

const props = defineProps<{
    task: Task;
}>();

// Inicializar flash messages
useFlashMessages();
</script>

<template>
    <AppLayout>
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Navbar -->
            <header
                class="flex items-center justify-between bg-gray-600 px-4 py-3 shadow"
            >
                <h1 class="text-xl font-bold text-white">Editar Tarefa</h1>
            </header>

            <div>
                <TaskForm :task="props.task" />
            </div>
        </div>
    </AppLayout>
</template>
