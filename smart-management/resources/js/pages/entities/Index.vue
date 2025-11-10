<template>
    <IndexWrapper
        :title="type === 'client' ? 'Clientes' : 'Fornecedores'"
        :description="`Gerir ${type === 'client' ? 'clientes' : 'fornecedores'} do sistema`"
        :columns="columns"
        :data="entities"
        :breadcrumbs="breadcrumbs"
        :create-button-text="
            type === 'client' ? 'Novo Cliente' : 'Novo Fornecedor'
        "
        :search-config="{
            enabled: true,
            placeholder: 'Buscar por nome ou NIF...',
        }"
        :filters-config="filterConfigs"
        :base-url="route.index().url"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import route from '@/routes/entities';
import {
    type BreadcrumbItem,
    type Country,
    type Entity,
    type PaginatedData,
} from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns } from './columns';

interface Props {
    type: 'client' | 'supplier';
    entities: PaginatedData<Entity>;
    filters: {
        search?: string;
        status?: string;
        country_id?: string;
    };
    countries: Country[];
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.type === 'client' ? 'Clientes' : 'Fornecedores',
        href: `${route.index().url}?type=${props.type}`,
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
            { value: 'active', label: 'Ativos' },
            { value: 'inactive', label: 'Inativos' },
        ],
    },
    {
        key: 'country_id',
        placeholder: 'País',
        allValue: 'all',
        allLabel: 'Todos os países',
        widthClass: 'w-[180px]',
        options: props.countries.map((country) => ({
            value: country.id,
            label: country.name,
        })),
    },
]);

// Methods
const handleCreate = () => {
    router.get(route.create().url, { type: props.type });
};
</script>
