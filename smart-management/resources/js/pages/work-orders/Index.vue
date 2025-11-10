<template>
    <IndexWrapper
        title="Ordens de Trabalho"
        description="Gerir ordens de trabalho"
        :columns="columns"
        :data="workOrders"
        :breadcrumbs="breadcrumbs"
        create-button-text="Nova Ordem de Trabalho"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar ordem...',
        }"
        :filters-config="filterConfigs"
        base-url="/work-orders"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import route from '@/routes/work-orders';
import { type BreadcrumbItem, type PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns, type WorkOrder } from './columns';

interface Props {
    workOrders: PaginatedData<WorkOrder>;
    filters: { search?: string; status?: string; priority?: string };
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ordens de Trabalho',
        href: '/work-orders',
    },
];

// Filter configurations
const filterConfigs = computed(() => [
    {
        key: 'status',
        placeholder: 'Estado',
        allValue: 'all',
        allLabel: 'Todos',
        widthClass: 'w-[150px]',
        options: [
            { value: 'pending', label: 'Pendentes' },
            { value: 'in_progress', label: 'Em Progresso' },
            { value: 'completed', label: 'Concluídas' },
            { value: 'cancelled', label: 'Canceladas' },
        ],
    },
    {
        key: 'priority',
        placeholder: 'Prioridade',
        allValue: 'all',
        allLabel: 'Todas',
        widthClass: 'w-[150px]',
        options: [
            { value: 'low', label: 'Baixa' },
            { value: 'medium', label: 'Média' },
            { value: 'high', label: 'Alta' },
            { value: 'urgent', label: 'Urgente' },
        ],
    },
]);

// Methods
const handleCreate = () => {
    router.get(route.create().url);
};
</script>
