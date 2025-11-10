<template>
    <IndexWrapper
        title="Encomendas de Fornecedores"
        description="Gerir encomendas enviadas aos fornecedores"
        :columns="columns"
        :data="supplierOrdersData"
        :breadcrumbs="breadcrumbs"
        :create-button-text="''"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar por número ou fornecedor...',
        }"
        :filters-config="filterConfigs"
        :base-url="supplierOrders.index().url"
        :current-filters="filters"
        :on-create="() => {}"
    >
        <template #header-actions>
            <div></div>
        </template>
    </IndexWrapper>
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import supplierOrders from '@/routes/supplier-orders';
import { type BreadcrumbItem, type PaginatedData } from '@/types';
import { computed } from 'vue';
import { columns, type SupplierOrder } from './columns';

interface Props {
    supplierOrdersData: PaginatedData<SupplierOrder>;
    filters: {
        search?: string;
        status?: string;
        supplier_id?: string;
    };
    suppliers: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Encomendas de Fornecedores',
        href: '/supplier-orders',
    },
];

// Filter configurations
const filterConfigs = computed(() => [
    {
        key: 'supplier_id',
        placeholder: 'Fornecedor',
        allValue: 'all',
        allLabel: 'Todos os Fornecedores',
        widthClass: 'w-[200px]',
        options: props.suppliers.map((supplier) => ({
            value: supplier.id,
            label: supplier.name,
        })),
    },
    {
        key: 'status',
        placeholder: 'Estado',
        allValue: 'all',
        allLabel: 'Todos',
        widthClass: 'w-[150px]',
        options: [
            { value: 'draft', label: 'Rascunho' },
            { value: 'closed', label: 'Fechada' },
        ],
    },
]);
</script>
