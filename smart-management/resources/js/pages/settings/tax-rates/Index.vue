<template>
    <IndexWrapper
        title="Taxas de Imposto"
        description="Gerir taxas de IVA e impostos"
        :columns="columns"
        :data="taxRatesData"
        :breadcrumbs="breadcrumbs"
        create-button-text="Nova Taxa"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar taxa...',
        }"
        :filters-config="filterConfigs"
        base-url="/tax-rates"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BreadcrumbItem, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns, type TaxRate } from './columns';

interface Props {
    taxRatesData: PaginatedData<TaxRate>;
    filters: { search?: string; is_active?: string };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Taxas de Imposto',
        href: '/tax-rates',
    },
];

// Filter configurations
const filterConfigs = computed(() => [
    {
        key: 'is_active',
        placeholder: 'Estado',
        allValue: 'all',
        allLabel: 'Todas',
        widthClass: 'w-[150px]',
        options: [
            { value: '1', label: 'Ativas' },
            { value: '0', label: 'Inativas' },
        ],
    },
]);

const handleCreate = () => {
    router.get('/tax-rates/create');
};
</script>
