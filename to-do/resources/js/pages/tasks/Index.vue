<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import Modal from '@/components/tasks/Modal.vue';
import TaskFilters from '@/components/tasks/TaskFilters.vue';
import TaskForm from '@/components/tasks/TaskForm.vue';
import TaskHeader from '@/components/tasks/TaskHeader.vue';
import TaskList from '@/components/tasks/TaskList.vue';
import { useFlashMessages } from '@/composables/useFlashMessages';
import { useTaskFilters } from '@/composables/useTaskFilters';
import AppLayout from '@/layouts/AppLayout.vue';
import routeTasks from '@/routes/tasks';
import { Filters, Task, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';

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

const showModal = ref(false);
const editingTask = ref<Task | null>(null);

const { filters, pushFilters } = useTaskFilters(props.filters);

function openCreate() {
    editingTask.value = null;
    showModal.value = true;
}

function openEdit(task: Task) {
    editingTask.value = task;
    showModal.value = true;
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
                    <button
                        type="button"
                        class="flex rounded bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                        @click="openCreate"
                    >
                        <Plus class="mr-1"></Plus>
                        Nova Tarefa
                    </button>
                </template>
            </TaskHeader>
            <TaskFilters :filters="filters" :pushFilters="pushFilters" />

            <main class="flex-1">
                <TaskList
                    :tasks="tasks.data"
                    @edit="openEdit"
                    @complete="handleComplete"
                    @delete="handleDelete"
                />

                <Pagination :paginator="tasks" :filters="filters" />
                <Modal
                    v-model="showModal"
                    :title="editingTask ? 'Editar' : 'Nova Tarefa'"
                >
                    <TaskForm
                        :task="editingTask || undefined"
                        @cancel="showModal = false"
                        @saved="showModal = false"
                    />
                </Modal>
            </main>
        </div>
    </AppLayout>
</template>
