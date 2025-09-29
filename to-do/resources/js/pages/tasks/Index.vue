<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import TaskHeader from '@/components/tasks/TaskHeader.vue';
import TaskList from '@/components/tasks/TaskList.vue';
import { useFlashMessages } from '@/composables/useFlashMessages';
import AppLayout from '@/layouts/AppLayout.vue';
import routeTasks from '@/routes/tasks';
import { Task, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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
    filters: {
        search?: string;
    };
}>();

// inicializar flash messages
useFlashMessages();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tarefas',
        href: routeTasks.index().url,
    },
];
const searchInput = ref(props.filters.search || '');

// debounce para busca
let searchTimeout: number;
watch(searchInput, (newSearch) => {
    clearTimeout(searchTimeout);
    searchTimeout = window.setTimeout(() => {
        router.get(
            routeTasks.index().url,
            {
                search: newSearch || undefined,
                page: 1, // reset para pag 1 ao buscar
            },
            {
                preserveState: true,
                preserveScroll: false,
                replace: true,
            },
        );
    }, 500);
});
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
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="relative">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400"
                    />
                    <input
                        type="text"
                        v-model="searchInput"
                        placeholder="Buscar por titulo ou descrição..."
                        class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            <main class="flex-1">
                <TaskList
                    :tasks="tasks.data"
                    @complete="handleComplete"
                    @delete="handleDelete"
                />

                <Pagination
                    :paginator="tasks"
                    :filters="filters"
                    @page-changed="(page) => console.log('pagina mudou:', page)"
                />
            </main>
        </div>
    </AppLayout>
</template>
