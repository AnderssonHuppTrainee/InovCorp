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
        status?: 'pending' | 'completed';
        priority?: 'low' | 'medium' | 'high';
        due_from?: string;
        due_to?: string;
        sort_by?: 'due_date' | 'priority' | 'title' | 'created_at';
        sort_dir?: 'asc' | 'desc';
        per_page?: number;
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
const statusInput = ref(props.filters.status || '');
const priorityInput = ref(props.filters.priority || '');
const dueFromInput = ref(props.filters.due_from || '');
const dueToInput = ref(props.filters.due_to || '');
const sortByInput = ref(props.filters.sort_by || 'due_date');
const sortDirInput = ref(props.filters.sort_dir || 'asc');
const perPageInput = ref(props.filters.per_page || 10);

// debounce para busca
let searchTimeout: number;
watch(searchInput, (newSearch) => {
    clearTimeout(searchTimeout);
    searchTimeout = window.setTimeout(() => {
        pushFilters({ search: newSearch || undefined });
    }, 400);
});

function pushFilters(extra: Record<string, unknown> = {}) {
    router.get(
        routeTasks.index().url,
        {
            search: searchInput.value || undefined,
            status: statusInput.value || undefined,
            priority: priorityInput.value || undefined,
            due_from: dueFromInput.value || undefined,
            due_to: dueToInput.value || undefined,
            sort_by: sortByInput.value || 'due_date',
            sort_dir: sortDirInput.value || 'asc',
            per_page: perPageInput.value || 10,
            page: 1,
            ...extra,
        },
        {
            preserveState: true,
            preserveScroll: false,
            replace: true,
        },
    );
}
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
            <div class="rounded-lg bg-white p-4 shadow-lg">
                <div
                    class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3"
                >
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

                    <div
                        class="flex flex-col gap-2 sm:flex-row sm:items-center"
                    >
                        <label class="text-sm text-gray-600">Estado:</label>
                        <select
                            v-model="statusInput"
                            @change="() => pushFilters()"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Todos</option>
                            <option value="pending">Pendente</option>
                            <option value="completed">Concluída</option>
                        </select>
                    </div>

                    <div
                        class="flex flex-col gap-2 sm:flex-row sm:items-center"
                    >
                        <label class="text-sm text-gray-600">Prioridade:</label>
                        <select
                            v-model="priorityInput"
                            @change="() => pushFilters()"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Todas</option>
                            <option value="low">Baixa</option>
                            <option value="medium">Média</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>
                </div>

                <div
                    class="mt-4 flex flex-col gap-3 md:grid-cols-2 md:flex-row md:items-center md:justify-between lg:grid-cols-4"
                >
                    <div
                        class="flex flex-col gap-2 sm:flex-row sm:items-center"
                    >
                        <input
                            type="date"
                            v-model="dueFromInput"
                            @change="() => pushFilters()"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            placeholder="De"
                        />
                        <input
                            type="date"
                            v-model="dueToInput"
                            @change="() => pushFilters()"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            placeholder="Até"
                        />
                    </div>
                    <div
                        class="flex flex-col gap-2 sm:flex-row sm:items-center"
                    >
                        <label class="text-sm text-gray-600">Ordenar por</label>
                        <div class="flex gap-2">
                            <select
                                v-model="sortByInput"
                                @change="() => pushFilters()"
                                class="rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="due_date">Data Limite</option>
                                <option value="priority">Prioridade</option>
                                <option value="title">Título</option>
                                <option value="created_at">
                                    Data de Criação
                                </option>
                            </select>
                            <select
                                v-model="sortDirInput"
                                @change="() => pushFilters()"
                                class="rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="asc">Ascendente</option>
                                <option value="desc">Descendente</option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="flex flex-col gap-2 sm:flex-row sm:items-center"
                    >
                        <label class="text-sm text-gray-600">Por página</label>
                        <select
                            v-model.number="perPageInput"
                            @change="() => pushFilters()"
                            class="rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        >
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="15">15</option>
                            <option :value="25">25</option>
                        </select>
                    </div>
                </div>
            </div>

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
