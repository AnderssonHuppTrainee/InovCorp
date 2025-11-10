<template>
    <IndexWrapper
        title="Encomendas"
        description="Gerir encomendas de clientes"
        :columns="columns"
        :data="orders"
        :breadcrumbs="breadcrumbs"
        create-button-text="Nova Encomenda"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar por número...',
        }"
        :filters-config="filterConfigs"
        base-url="/orders"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BreadcrumbItem, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Order } from './columns';
import { columns } from './columns';

interface Client {
    id: number;
    name: string;
}

interface Props {
    orders: PaginatedData<Order>;
    filters: {
        search?: string;
        status?: string;
        client_id?: string;
    };
    clients: Client[];
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Encomendas',
        href: '/orders',
    },
];

// Filter configurations
const filterConfigs = computed(() => [
    {
        key: 'status',
        placeholder: 'Status',
        allValue: 'all',
        allLabel: 'Todos',
        widthClass: 'w-[150px]',
        options: [
            { value: 'draft', label: 'Rascunho' },
            { value: 'closed', label: 'Fechado' },
        ],
    },
    {
        key: 'client_id',
        placeholder: 'Cliente',
        allValue: 'all',
        allLabel: 'Todos os clientes',
        widthClass: 'w-[200px]',
        options: props.clients.map((client) => ({
            value: client.id,
            label: client.name,
        })),
    },
]);

// Methods
const handleCreate = () => {
    router.get('/orders/create');
};
</script>
