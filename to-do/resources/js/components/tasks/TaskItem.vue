<template>
    <div class="p-4">
        <li
            class="rounded-lg bg-white p-6 shadow-md transition focus-within:ring-2 focus-within:ring-blue-500 hover:shadow-lg"
            :class="{
                'line-through opacity-60': props.task.status === 'completed',
            }"
        >
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
            >
                <h3 class="text-lg font-bold text-gray-900">
                    <Link
                        :href="routeTasks.show(props.task.id).url"
                        class="hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        :aria-label="`Ver detalhes da tarefa ${props.task.title}`"
                    >
                        {{ props.task.title }}
                    </Link>
                </h3>
                <span
                    v-if="priorityConfig[props.task.priority]"
                    class="mt-2 inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold text-white sm:mt-0"
                    :class="priorityConfig[props.task.priority].color"
                    :aria-label="`Prioridade ${priorityConfig[props.task.priority].label}`"
                    role="status"
                >
                    <component
                        :is="priorityConfig[props.task.priority].icon"
                        class="h-3 w-3"
                    />
                    {{ priorityConfig[props.task.priority].label }}
                </span>
            </div>

            <p class="mt-2 text-sm text-gray-600">
                {{ props.task.description }}
            </p>

            <div class="mt-4 flex justify-between">
                <div>
                    <label class="font-medium text-gray-700"
                        >Data Limite:</label
                    >
                    <p class="mt-1 text-gray-800">
                        {{ props.task.due_date || '—' }}
                    </p>
                </div>
                <div>
                    <Link
                        :href="routeTasks.show(props.task.id).url"
                        title="Detalhes"
                        class="text-gray-900 transition hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        aria-label="Abrir detalhes"
                    >
                        <Eye />
                    </Link>
                </div>
            </div>
            <div class="mt-4">
                <div v-if="props.task.status === 'completed'">
                    <span
                        class="mt-2 inline-block rounded-full bg-green-500 px-3 py-1 text-xs font-semibold text-white sm:mt-0"
                        :aria-label="`Status ${props.task.status}`"
                        role="status"
                    >
                        {{ statusLabels[props.task.status] }}
                    </span>
                </div>
                <div v-if="isOverdue(props.task)">
                    <span
                        class="mt-2 inline-block rounded-full bg-red-600 px-3 py-1 text-xs font-semibold text-white sm:mt-0"
                    >
                        Atrasado
                    </span>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap justify-end gap-3">
                <button
                    v-if="props.task.status !== 'completed'"
                    class="flex items-center gap-1 text-green-600 transition hover:text-green-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600"
                    @click="$emit('complete', props.task.id)"
                    title="Marcar como concluída"
                    aria-label="Marcar como concluída"
                >
                    <SquareCheck />
                    Concluir
                </button>

                <button
                    type="button"
                    v-if="props.task.status !== 'completed'"
                    @click="$emit('edit', props.task)"
                    class="flex items-center gap-1 text-yellow-600 transition hover:text-yellow-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-600"
                    title="Editar tarefa"
                    aria-label="Editar tarefa"
                >
                    <SquarePen />
                    Editar
                </button>

                <button
                    class="flex items-center gap-1 text-red-600 transition hover:text-red-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600"
                    @click="$emit('delete', props.task.id)"
                    title="Deletar tarefa"
                    aria-label="Deletar tarefa"
                >
                    <Trash2 />
                    Excluir
                </button>
            </div>
        </li>
    </div>
</template>

<script setup lang="ts">
import routeTasks from '@/routes/tasks';
import { Task } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    ArrowDown,
    CircleAlert,
    Eye,
    Flag,
    SquareCheck,
    SquarePen,
    Trash2,
} from 'lucide-vue-next';

const props = defineProps<{
    task: Task; //array de tasks
}>();

const priorityConfig: Record<
    string,
    { label: string; icon: any; color: string }
> = {
    high: {
        label: 'Alta',
        icon: Flag,
        color: 'bg-red-500 text-white dark:bg-red-900/30 dark:text-red-300',
    },
    medium: {
        label: 'Média',
        icon: CircleAlert,
        color: 'bg-yellow-300 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    },
    low: {
        label: 'Baixa',
        icon: ArrowDown,
        color: 'bg-green-500 text-white dark:bg-green-900/30 dark:text-green-300',
    },
};
const statusLabels: Record<string, string> = {
    completed: 'Concluído',
    pending: 'Pendente',
};

function isOverdue(task: { status: string; due_date?: string | null }) {
    if (task.status !== 'pending' || !task.due_date) return false;

    const today = new Date();
    const dueDate = new Date(task.due_date);

    return dueDate < new Date(today.toISOString().split('T')[0]);
}
</script>
