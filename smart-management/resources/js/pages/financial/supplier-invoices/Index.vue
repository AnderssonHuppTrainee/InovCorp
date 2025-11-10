<template>
    <IndexWrapper
        title="Faturas de Fornecedor"
        description="Gerir faturas de fornecedores"
        :columns="columns"
        :data="invoices"
        :breadcrumbs="breadcrumbs"
        create-button-text="Nova Fatura"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar fatura...',
        }"
        :filters-config="filterConfigs"
        base-url="/supplier-invoices"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BreadcrumbItem, Entity, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns, SupplierInvoice } from './columns';

interface Props {
    invoices: PaginatedData<SupplierInvoice>;
    filters: { search?: string; status?: string; supplier_id?: string };
    suppliers: Entity[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Faturas de Fornecedor',
        href: '/supplier-invoices',
    },
];

// Filter configurations
const filterConfigs = computed(() => [
    {
        key: 'status',
        placeholder: 'Estado',
        allValue: 'all',
        allLabel: 'Todas',
        widthClass: 'w-[150px]',
        options: [
            { value: 'draft', label: 'Rascunho' },
            { value: 'sent', label: 'Enviadas' },
            { value: 'paid', label: 'Pagas' },
            { value: 'overdue', label: 'Vencidas' },
        ],
    },
    {
        key: 'supplier_id',
        placeholder: 'Fornecedor',
        allValue: 'all',
        allLabel: 'Todos',
        widthClass: 'w-[180px]',
        options: props.suppliers.map((supplier) => ({
            value: supplier.id,
            label: supplier.name,
        })),
    },
]);

const handleCreate = () => {
    router.get('/supplier-invoices/create');
};
</script>
