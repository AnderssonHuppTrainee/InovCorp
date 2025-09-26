<template>
    <div class="rounded bg-white p-6">
        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="title">Título</Label>
                    <Input
                        id="title"
                        type="text"
                        v-model="form.title"
                        required
                        autofocus
                        autocomplete="title"
                        name="title"
                        placeholder="Study English"
                    />
                    <span v-if="form.errors.title" class="text-sm text-red-500">
                        {{ form.errors.title }}
                    </span>
                </div>

                <div class="grid gap-2">
                    <Label for="description">Descrição</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Descrição (opcional)"
                        class="w-full rounded border px-3 py-2"
                    ></textarea>
                    <span
                        v-if="form.errors.description"
                        class="text-sm text-red-500"
                    >
                        {{ form.errors.description }}
                    </span>
                </div>

                <div class="grid gap-2">
                    <Label for="priority">Prioridade</Label>
                    <select
                        id="priority"
                        v-model="form.priority"
                        class="w-full rounded border px-3 py-2"
                    >
                        <option value="low">Baixa</option>
                        <option value="medium">Média</option>
                        <option value="high">Alta</option>
                    </select>
                    <span
                        v-if="form.errors.priority"
                        class="text-sm text-red-500"
                    >
                        {{ form.errors.priority }}
                    </span>
                </div>

                <div class="grid gap-2">
                    <Label for="due_date">Data Limite</Label>
                    <Input
                        id="due_date"
                        type="date"
                        v-model="form.due_date"
                        autocomplete="due_date"
                        name="due_date"
                    />
                    <span
                        v-if="form.errors.due_date"
                        class="text-sm text-red-500"
                    >
                        {{ form.errors.due_date }}
                    </span>
                </div>

                <div class="flex justify-end gap-2">
                    <Button type="submit" class="mt-2 w-50" tabindex="5">
                        <LoaderCircle
                            v-if="form.processing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        Salvar
                    </Button>

                    <div
                        class="mt-2 flex w-50 items-center justify-center rounded hover:bg-gray-300"
                    >
                        <a :href="routeTasks.index().url"> Cancelar </a>
                    </div>
                </div>
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
    task: Task;
}>();

const form = useForm({
    title: props.task?.title || '',
    description: props.task?.description || '',
    priority: props.task?.priority || 'medium',
    due_date: props.task?.due_date || '',
});

function handleSubmit() {
    if (props.task) {
        //update
        form.patch(routeTasks.update(props.task.id).url);
    } else {
        // create
        form.post(routeTasks.store().url);
    }
}
</script>
