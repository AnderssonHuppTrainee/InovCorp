<script setup lang="ts">
import TaskList from '@/components/tasks/TaskList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Task } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const tasks = ref<Task[]>([
    {
        id: 1,
        title: 'Estudar Vue',
        description: 'Finalizar CRUD com Laravel',
        priority: 'high',
        status: 'pending',
    },
    {
        id: 2,
        title: 'Revisar Tailwind',
        description: 'Criar layout responsivo',
        priority: 'high',
        status: 'pending',
    },
]);

const createTask = ref(false);

const completeTask = (id: number) => {
    const task = tasks.value.find((t) => t.id === id);
    if (task) task.status = 'completed';
};

const deleteTask = (id: number) => {
    tasks.value = tasks.value.filter((t) => t.id !== id);
};
</script>

<template>
    <AppLayout>
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <header
                class="flex items-center justify-between bg-gray-600 px-4 py-3 shadow"
            >
                <h1 class="text-xl font-bold text-white">Gestão de Tarefas</h1>
                <Link
                    class="rounded bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                    href="route('tasks.create')"
                >
                    Nova Tarefa
                </Link>
            </header>
            <main class="flex-1 p-4">
                <TaskList
                    :tasks="tasks"
                    @complete="completeTask"
                    @delete="deleteTask"
                />
            </main>
        </div>
    </AppLayout>
</template>
