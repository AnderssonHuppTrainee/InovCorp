<template>
    <div class="p-4">
        <li
            class="rounded-lg bg-white p-6 shadow-md transition hover:shadow-lg focus-within:ring-2 focus-within:ring-blue-500"
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
                    class="mt-2 inline-block rounded-full px-3 py-1 text-xs font-semibold text-white sm:mt-0"
                    :class="{
                        'bg-red-500': props.task.priority === 'high',
                        'bg-yellow-500': props.task.priority === 'medium',
                        'bg-green-500': props.task.priority === 'low',
                    }"
                    :aria-label="`Prioridade ${props.task.priority}`"
                    role="status"
                >
                    {{ props.task.priority }}
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
                    <p class="mt-1 text-gray-800">{{ props.task.due_date || '—' }}</p>
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

                <Link
                    :href="routeTasks.edit(props.task.id).url"
                    class="flex items-center gap-1 text-yellow-600 transition hover:text-yellow-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-600"
                    title="Editar tarefa"
                    aria-label="Editar tarefa"
                >
                    <SquarePen />
                    Editar
                </Link>

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
import { Eye, SquareCheck, SquarePen, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    task: Task; //array de tasks
}>();
</script>
