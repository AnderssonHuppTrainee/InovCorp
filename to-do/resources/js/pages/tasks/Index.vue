<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import TaskFilters from '@/components/tasks/TaskFilters.vue';
import TaskHeader from '@/components/tasks/TaskHeader.vue';
import TaskList from '@/components/tasks/TaskList.vue';
import { useFlashMessages } from '@/composables/useFlashMessages';
import { useTaskFilters } from '@/composables/useTaskFilters';
import AppLayout from '@/layouts/AppLayout.vue';
import routeTasks from '@/routes/tasks';
import { Filters, Task, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    tasks: {
        data: Task[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
        links: any[];
    };
    filters: Filters;
}>();

// inicializar flash messages
useFlashMessages();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tarefas',
        href: routeTasks.index().url,
    },
];

const { filters, pushFilters } = useTaskFilters(props.filters);

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
            <TaskHeader title="Lista de Tarefas">
                <template #actions>
                    <Link
                        class="rounded bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                        :href="routeTasks.create().url"
                    >
                        Nova Tarefa
                    </Link>
                </template>
            </TaskHeader>
            <TaskFilters :filters="filters" :pushFilters="pushFilters" />

            <main class="flex-1">
                <TaskList
                    :tasks="tasks.data"
                    @complete="handleComplete"
                    @delete="handleDelete"
                />

                <Pagination :paginator="tasks" :filters="filters" />
            </main>
        </div>
    </AppLayout>
</template>
