<template>
    <IndexWrapper
        title="Tipos de Evento"
        description="Gerir tipos de eventos do calendário"
        :columns="columns"
        :data="calendarEventTypesData"
        :breadcrumbs="breadcrumbs"
        create-button-text="Novo Tipo"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar tipo...',
        }"
        :filters-config="filterConfigs"
        base-url="/calendar-event-types"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BreadcrumbItem, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns, type CalendarEventType } from './columns';

interface Props {
    calendarEventTypesData: PaginatedData<CalendarEventType>;
    filters: { search?: string; is_active?: string };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tipos de Evento',
        href: '/calendar-event-types',
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
    router.get('/calendar-event-types/create');
};
</script>
