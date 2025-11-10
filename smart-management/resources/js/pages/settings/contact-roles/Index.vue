<template>
    <IndexWrapper
        title="Funções de Contacto"
        description="Gerir funções disponíveis para contactos"
        :columns="columns"
        :data="contactRolesData"
        :breadcrumbs="breadcrumbs"
        create-button-text="Nova Função"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar função...',
        }"
        :filters-config="filterConfigs"
        base-url="/contact-roles"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BreadcrumbItem, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns, type ContactRole } from './columns';

interface Props {
    contactRolesData: PaginatedData<ContactRole>;
    filters: { search?: string; is_active?: string };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Funções de Contacto',
        href: '/contact-roles',
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
    router.get('/contact-roles/create');
};
</script>
