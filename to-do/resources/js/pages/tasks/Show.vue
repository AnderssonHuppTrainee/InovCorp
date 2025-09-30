<script setup lang="ts">
import TaskHeader from '@/components/tasks/TaskHeader.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import routeTasks from '@/routes/tasks';
import { BreadcrumbItem, Task } from '@/types';
import { Link } from '@inertiajs/vue3';
import { MoveLeft, SquarePen, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    task: Task;
}>();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.task.title,
        href: routeTasks.index().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mt-4 flex px-4">
            <Link
                :href="routeTasks.index().url"
                class="flex rounded bg-white px-3 py-1 text-gray-800 hover:bg-gray-300"
            >
                <MoveLeft class="mr-2" />
                Voltar
            </Link>
        </div>
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <TaskHeader title="Detalhes da Tarefa" />

            <div class="grid gap-4 rounded-lg bg-white p-6 shadow">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">
                        {{ props.task.title }}
                    </h2>
                    <p class="mt-2 whitespace-pre-line text-gray-700">
                        {{ props.task.description }}
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div class="rounded border p-4">
                        <div class="text-sm text-gray-500">Prioridade</div>
                        <div class="mt-1 font-medium capitalize">
                            {{ props.task.priority }}
                        </div>
                    </div>
                    <div class="rounded border p-4">
                        <div class="text-sm text-gray-500">Estado</div>
                        <div class="mt-1 font-medium capitalize">
                            {{ props.task.status }}
                        </div>
                    </div>
                    <div class="rounded border p-4">
                        <div class="text-sm text-gray-500">Data Limite</div>
                        <div class="mt-1 font-medium">
                            {{ props.task.due_date || '—' }}
                        </div>
                    </div>
                </div>

                <div class="text-sm text-gray-500">
                    Criado em:
                    <span class="font-medium text-gray-700">{{
                        props.task.created_at
                    }}</span>
                    <span class="mx-2">•</span>
                    Atualizado em:
                    <span class="font-medium text-gray-700">{{
                        props.task.updated_at
                    }}</span>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="routeTasks.edit(props.task.id).url"
                        class="flex rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600"
                    >
                        <SquarePen class="mr-2" />Editar</Link
                    >
                    <Link
                        :href="routeTasks.edit(props.task.id).url"
                        class="flex rounded bg-red-500 px-3 py-1 text-white hover:bg-red-600"
                    >
                        <Trash2 class="mr-2" />
                        Excluir</Link
                    >
                </div>
            </div>
        </div>
    </AppLayout>
</template>
