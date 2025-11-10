<template>
    <IndexWrapper
        title="Logs de Atividade"
        description="Histórico de ações no sistema"
        :columns="columns"
        :data="logs"
        :breadcrumbs="breadcrumbs"
        :create-button-text="''"
        :search-config="{
            enabled: false,
            placeholder: '',
        }"
        :filters-config="[]"
        base-url="/settings/logs"
        :current-filters="{}"
        :on-create="() => {}"
    >
        <template #header-actions>
            <div></div>
        </template>
    </IndexWrapper>
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import { type BreadcrumbItem, type PaginatedData } from '@/types';
import { columns, type Log } from './columns';

interface Props {
    logs: PaginatedData<Log>;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Logs de Atividade',
        href: '/settings/logs',
    },
];
</script>
