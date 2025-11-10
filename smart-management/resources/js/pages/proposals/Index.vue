<template>
    <IndexWrapper
        title="Propostas"
        description="Gerir propostas comerciais"
        :columns="columns"
        :data="proposals"
        :breadcrumbs="breadcrumbs"
        create-button-text="Nova Proposta"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar proposta...',
        }"
        :filters-config="filterConfigs"
        base-url="/proposals"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import route from '@/routes/proposals';
import { type BreadcrumbItem, type PaginatedData } from '@/types';
import type { Proposal } from './columns';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns } from './columns';

interface Props {
    proposals: PaginatedData<Proposal>;
    filters: { search?: string; status?: string };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gestão de Propostas',
        href: '/proposals',
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
            { value: 'closed', label: 'Fechadas' },
        ],
    },
]);

const handleCreate = () => {
    router.get(route.create().url);
};
</script>
