<script setup lang="ts">
import TaskForm from '@/components/tasks/TaskForm.vue';
import TaskHeader from '@/components/tasks/TaskHeader.vue';
import { useFlashMessages } from '@/composables/useFlashMessages';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nova Tarefa',
        href: '/tasks',
    },
];

// Inicializar flash messages
useFlashMessages();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <TaskHeader title="Criar Nova Tarefa" />

            <div>
                <TaskForm />
            </div>
        </div>
    </AppLayout>
</template>
