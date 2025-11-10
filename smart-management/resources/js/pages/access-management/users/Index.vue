<template>
    <IndexWrapper
        title="Utilizadores"
        description="Gerir utilizadores do sistema"
        :columns="columns"
        :data="users"
        :breadcrumbs="breadcrumbs"
        create-button-text="Novo Utilizador"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar utilizador...',
        }"
        :filters-config="filterConfigs"
        :base-url="route.index().url"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import route from '@/routes/users';
import type { BreadcrumbItem, PaginatedData, User } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns } from './columns';

interface Role {
    id: number;
    name: string;
}

interface Props {
    users: PaginatedData<User>;
    filters: { search?: string; role_id?: string; status?: string };
    roles: Role[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Utilizadores',
        href: route.index().url,
    },
];

// Filter configurations
const filterConfigs = computed(() => [
    {
        key: 'role_id',
        placeholder: 'Função',
        allValue: 'all',
        allLabel: 'Todas',
        widthClass: 'w-[150px]',
        options: props.roles.map((role) => ({
            value: role.id,
            label: role.name,
        })),
    },
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
