<template>
    <IndexWrapper
        title="Contas Bancárias"
        description="Gerir contas bancárias da empresa"
        :columns="columns"
        :data="accounts"
        :filters="filters"
        :breadcrumbs="breadcrumbs"
        base-url="/bank-accounts"
        :current-filters="filters"
        :search-config="{
            enabled: true,
            placeholder: 'Buscar por nome ou banco...',
        }"
        :filters-config="filterConfigs"
        :on-create="handleCreate"
    >
    </IndexWrapper>
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import type { BankAccount, BreadcrumbItem, PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import { columns } from './columns';

interface Props {
    accounts: PaginatedData<BankAccount>;
    filters: {
        search?: string;
        is_active?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contas Bancárias',
        href: '/bank-accounts',
    },
];

const filterConfigs = [
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
];

const handleCreate = () => {
    router.get('/bank-accounts/create');
};
</script>
