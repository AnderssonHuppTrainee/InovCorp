<template>
    <div class="mx-auto max-w-2xl rounded-2xl bg-white p-6 shadow-md sm:p-8">
        <form
            @submit.prevent="handleSubmit"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            <!-- Título -->
            <div class="space-y-2">
                <Label for="title">Título</Label>
                <Input
                    id="title"
                    type="text"
                    v-model="form.title"
                    required
                    autofocus
                    autocomplete="title"
                    placeholder="Ex: Estudar inglês"
                />
                <p v-if="form.errors.title" class="text-sm text-red-500">
                    {{ form.errors.title }}
                </p>
            </div>

            <!-- Descrição -->
            <div class="space-y-2">
                <Label for="description">Descrição</Label>
                <textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Descrição (opcional)"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    rows="4"
                ></textarea>
                <p v-if="form.errors.description" class="text-sm text-red-500">
                    {{ form.errors.description }}
                </p>
            </div>

            <!-- Prioridade e Data -->
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="priority">Prioridade</Label>
                    <select
                        id="priority"
                        v-model="form.priority"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="low">Baixa</option>
                        <option value="medium">Média</option>
                        <option value="high">Alta</option>
                    </select>
                    <p v-if="form.errors.priority" class="text-sm text-red-500">
                        {{ form.errors.priority }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="due_date">Data Limite</Label>
                    <Input
                        id="due_date"
                        type="date"
                        v-model="form.due_date"
                        autocomplete="due_date"
                    />
                    <p v-if="form.errors.due_date" class="text-sm text-red-500">
                        {{ form.errors.due_date }}
                    </p>
                </div>
            </div>

            <!-- Ações -->
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a
                    :href="routeTasks.index().url"
                    class="flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 transition hover:bg-gray-100"
                >
                    Cancelar
                </a>

                <Button
                    type="submit"
                    class="flex items-center justify-center gap-2 bg-green-500 text-white hover:bg-green-600"
                    :disabled="form.processing"
                >
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Salvar
                </Button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import routeTasks from '@/routes/tasks';
import { Task } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const props = defineProps<{
    task?: Task;
}>();

const form = useForm({
    title: props.task?.title || '',
    description: props.task?.description || '',
    priority: props.task?.priority || 'medium',
    due_date: props.task?.due_date || '',
});

function handleSubmit() {
    if (props.task) {
        form.patch(routeTasks.update(props.task.id).url);
    } else {
        form.post(routeTasks.store().url);
    }
}
</script>
