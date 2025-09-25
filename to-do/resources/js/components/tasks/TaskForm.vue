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
                </div>

                <div class="grid gap-2">
                    <Label for="description">Descrição</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Descrição (opcional)"
                        class="w-full rounded border px-3 py-2"
                    ></textarea>
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
                </div>

                <div class="grid gap-2">
                    <Label for="dueDate">Data Limite</Label>
                    <Input
                        id="dueDate"
                        type="date"
                        v-model="form.dueDate"
                        required
                        autocomplete="dueDate"
                        name="dueDate"
                    />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5">
                    <LoaderCircle
                        v-if="processing"
                        class="mr-2 h-4 w-4 animate-spin"
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
import { LoaderCircle } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

const form = reactive({
    title: '',
    description: '',
    priority: 'medium',
    dueDate: '',
});

const processing = ref(false);

function handleSubmit() {
    processing.value = true;

    // Simulação de envio
    setTimeout(() => {
        console.log('Form enviado:', { ...form });
        processing.value = false;

        // Resetar formulário
        form.title = '';
        form.description = '';
        form.priority = 'medium';
        form.dueDate = '';
    }, 1500);
}
</script>
