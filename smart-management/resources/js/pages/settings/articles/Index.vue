<template>
    <IndexWrapper
        title="Artigos"
        description="Gerir catálogo de artigos"
        :columns="columns"
        :data="articles"
        :breadcrumbs="breadcrumbs"
        create-button-text="Novo Artigo"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar artigo...',
        }"
        :filters-config="filterConfigs"
        base-url="/articles"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import route from '@/routes/articles';
import type { BreadcrumbItem, PaginatedData, Article } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns } from './columns';

interface Props {
    articles: PaginatedData<Article>;
    filters: { search?: string; status?: string };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gestão de Artigos',
        href: route.index().url,
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
            { value: 'active', label: 'Ativos' },
            { value: 'inactive', label: 'Inativos' },
        ],
    },
]);

const handleCreate = () => {
    router.get(route.create().url);
};
</script>
