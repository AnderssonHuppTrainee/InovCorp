<template>
    <IndexWrapper
        title="Faturas de Cliente"
        description="Gerir faturas de clientes"
        :columns="columns"
        :data="invoices"
        :breadcrumbs="breadcrumbs"
        create-button-text="Nova Fatura"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar fatura...',
        }"
        :filters-config="filterConfigs"
        base-url="/customer-invoices"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BreadcrumbItem, Customer, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { CustomerInvoice } from './columns';
import { columns } from './columns';

interface Props {
    invoices: PaginatedData<CustomerInvoice>;
    filters: { search?: string; status?: string; customer_id?: string };
    customers: Customer[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Faturas de Cliente',
        href: '/customer-invoices',
    },
];

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
        key: 'customer_id',
        placeholder: 'Cliente',
        allValue: 'all',
        allLabel: 'Todos',
        widthClass: 'w-[180px]',
        options: props.customers.map((customer) => ({
            value: customer.id,
            label: customer.name,
        })),
    },
]);

const handleCreate = () => {
    router.get('/customer-invoices/create');
};
</script>
