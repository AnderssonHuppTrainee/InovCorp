<template>
    <IndexWrapper
        title="Contactos"
        description="Gerir contactos das entidades"
        :columns="columns"
        :data="contacts"
        :breadcrumbs="breadcrumbs"
        create-button-text="Novo Contacto"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar por nome, email ou telefone...',
        }"
        :filters-config="filterConfigs"
        base-url="/contacts"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type {
    BreadcrumbItem,
    ContactRole,
    Entity,
    PaginatedData,
} from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns, type Contact } from './columns';

interface Props {
    contacts: PaginatedData<Contact>;
    filters: {
        search?: string;
        status?: string;
        entity_id?: string;
        contact_role_id?: string;
    };
    entities: Entity[];
    roles: ContactRole[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contactos',
        href: '/contacts',
    },
];

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
        key: 'entity_id',
        placeholder: 'Entidade',
        allValue: 'all',
        allLabel: 'Todas as entidades',
        widthClass: 'w-[180px]',
        options: props.entities.map((entity) => ({
            value: entity.id,
            label: entity.name,
        })),
    },
    {
        key: 'contact_role_id',
        placeholder: 'Função',
        allValue: 'all',
        allLabel: 'Todas as funções',
        widthClass: 'w-[180px]',
        options: props.roles.map((role) => ({
            value: role.id,
            label: role.name,
        })),
    },
]);

// Methods
const handleCreate = () => {
    router.get('/contacts/create');
};
</script>
