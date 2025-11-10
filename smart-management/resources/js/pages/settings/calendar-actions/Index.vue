<template>
    <IndexWrapper
        title="Ações de Calendário"
        description="Gerir ações disponíveis para eventos"
        :columns="columns"
        :data="calendarActionsData"
        :breadcrumbs="breadcrumbs"
        create-button-text="Nova Ação"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar ação...',
        }"
        :filters-config="filterConfigs"
        base-url="/calendar-actions"
        :current-filters="filters"
        :on-create="handleCreate"
    />
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BreadcrumbItem, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { columns, type CalendarAction } from './columns';

interface Props {
    calendarActionsData: PaginatedData<CalendarAction>;
    filters: { search?: string; is_active?: string };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ações de Calendário',
        href: '/calendar-actions',
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
    router.get('/calendar-actions/create');
};
</script>
