<script setup lang="ts">
import TaskList from '@/components/tasks/TaskList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import routeTasks from '@/routes/tasks';
import { Task, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { useFlashMessages } from '@/composables/useFlashMessages';

defineProps<{
    tasks: Task[];
}>();

// Inicializar flash messages
useFlashMessages();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tarefas',
        href: routeTasks.index().url,
    },
];

const handleComplete = (taskId: number) => {
    router.patch(
        routeTasks.complete(taskId).url,
        {
            status: 'completed',
        },
        {
            onSuccess: () => {
                console.log('Tarefa completada!');
            },
        },
    );
};

const handleDelete = (taskId: number) => {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        router.delete(routeTasks.destroy(taskId).url, {
            onSuccess: () => {
                console.log('Tarefa excluída!');
            },
        });
    }
};
</script>

<template>
    <Head title="Tarefas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <header
                class="flex items-center justify-between bg-gray-600 px-4 py-3 shadow"
            >
                <h1 class="text-xl font-bold text-white">Gestão de Tarefas</h1>
                <Link
                    class="rounded bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                    :href="routeTasks.create().url"
                >
                    Nova Tarefa
                </Link>
            </header>
            <main class="flex-1 p-4">
                <TaskList
                    :tasks="tasks"
                    @complete="handleComplete"
                    @delete="handleDelete"
                />
            </main>
        </div>
    </AppLayout>
</template>
