<template>
    <IndexWrapper
        title="Países"
        description="Gerir países disponíveis no sistema"
        :columns="columns"
        :data="countries"
        :breadcrumbs="breadcrumbs"
        create-button-text="Novo País"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar país...',
        }"
        :filters-config="filterConfigs"
        base-url="/countries"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BreadcrumbItem, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns, type Country } from './columns';

interface Props {
    countries: PaginatedData<Country>;
    filters: { search?: string; is_active?: string };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Países',
        href: '/countries',
    },
];

// Filter configurations
const filterConfigs = computed(() => [
    {
        key: 'is_active',
        placeholder: 'Estado',
        allValue: 'all',
        allLabel: 'Todos',
        widthClass: 'w-[150px]',
        options: [
            { value: '1', label: 'Ativos' },
            { value: '0', label: 'Inativos' },
        ],
    },
]);

const handleCreate = () => {
    router.get('/countries/create');
};
</script>
