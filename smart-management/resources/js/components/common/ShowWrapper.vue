<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <!-- Header com título, descrição e ações -->
            <PageHeader :title="title" :description="description">
                <slot name="header-actions">
                    <div class="flex gap-2">
                        <Button variant="outline" @click="handleGoBack">
                            <ArrowLeftIcon class="mr-2 h-4 w-4" />
                            Voltar
                        </Button>
                        <Button @click="handleEdit">
                            <PencilIcon class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                        <Button variant="destructive" @click="handleDelete">
                            <Trash2Icon class="mr-2 h-4 w-4" />
                            Eliminar
                        </Button>
                    </div>
                </slot>
            </PageHeader>

            <!-- Layout principal com grid responsivo -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Coluna Principal (2/3) -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Slot para conteúdo principal -->
                    <slot name="main-content" />
                </div>

                <!-- Coluna Lateral (1/3) -->
                <div class="space-y-6">
                    <!-- Slot para sidebar -->
                    <slot name="sidebar" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, PencilIcon, Trash2Icon } from 'lucide-vue-next';

// Types
interface Props {
    title: string;
    description?: string;
    editUrl?: string;
    deleteUrl?: string;
    backUrl?: string;
    itemName?: string; // Para confirmação de exclusão
    onEdit?: () => void;
    onDelete?: () => void;
    onBack?: () => void;
}

const props = withDefaults(defineProps<Props>(), {
    description: '',
    itemName: 'item',
});

// Methods
const handleGoBack = () => {
    if (typeof props.onBack === 'function') {
        props.onBack();
        return;
    }
    if (props.backUrl) {
        router.get(props.backUrl);
    }
};

const handleEdit = () => {
    if (typeof props.onEdit === 'function') {
        props.onEdit();
        return;
    }
    if (props.editUrl) {
        router.get(props.editUrl);
    }
};

const handleDelete = () => {
    if (typeof props.onDelete === 'function') {
        props.onDelete();
        return;
    }
    if (props.deleteUrl) {
        const confirmed = confirm(
            `Tem certeza que deseja eliminar "${props.itemName}"?\n\nEsta ação não pode ser desfeita.`,
        );

        if (confirmed) {
            router.delete(props.deleteUrl, {
                onSuccess: () => {
                    if (props.backUrl) {
                        router.get(props.backUrl);
                    }
                },
            });
        }
    }
};
</script>

