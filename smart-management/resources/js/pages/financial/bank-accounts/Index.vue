<template>
    <Head title="Contas Bancárias" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                title="Contas Bancárias"
                description="Gerir contas bancárias da empresa"
            >
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Nova Conta
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center"
                    >
                        <div class="flex flex-1 gap-2">
                            <div class="relative max-w-sm flex-1">
                                <SearchIcon
                                    class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground"
                                />
                                <Input
                                    type="search"
                                    placeholder="Buscar por nome ou banco..."
                                    class="pl-8"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                />
                            </div>

                            <Select
                                v-model="statusFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[150px]">
                                    <SelectValue placeholder="Estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todas</SelectItem>
                                    <SelectItem value="1">Ativas</SelectItem>
                                    <SelectItem value="0">Inativas</SelectItem>
                                </SelectContent>
                            </Select>

                            <Button
                                variant="ghost"
                                @click="clearFilters"
                                v-if="hasFilters"
                            >
                                <XIcon class="mr-2 h-4 w-4" />
                                Limpar
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <DataTable :columns="columns" :data="accounts.data" />

                    <div
                        class="flex items-center justify-between px-2 py-4"
                        v-if="accounts.data.length > 0"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ accounts.from }}</strong> a
                            <strong>{{ accounts.to }}</strong> de
                            <strong>{{ accounts.total }}</strong> resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!accounts.prev_page_url"
                                @click="goToPage(accounts.current_page - 1)"
                            >
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>

                            <div class="flex items-center gap-1">
                                <Button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    :variant="
                                        page === accounts.current_page
                                            ? 'default'
                                            : 'outline'
                                    "
                                    size="sm"
                                    @click="goToPage(page)"
                                    class="w-9"
                                >
                                    {{ page }}
                                </Button>
                            </div>

                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!accounts.next_page_url"
                                @click="goToPage(accounts.current_page + 1)"
                            >
                                Próxima
                                <ChevronRightIcon class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import DataTable from '@/components/ui/data-table/DataTable.vue';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    PlusIcon,
    SearchIcon,
    XIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { columns } from './columns';

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    from: number;
    to: number;
    total: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface Props {
    accounts: PaginatedData<any>;
    filters: { search?: string; is_active?: string };
}

const props = defineProps<Props>();

// Toast
const { showSuccess, showInfo, showError, showWarning } = useToast();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contas Bancárias',
        href: '/bank-accounts',
    },
];

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.is_active || 'all');

const hasFilters = computed(() => {
    return searchQuery.value !== '' || statusFilter.value !== 'all';
});

const visiblePages = computed(() => {
    const current = props.accounts.current_page;
    const last = props.accounts.last_page;
    const delta = 2;
    const pages: number[] = [];

    for (
        let i = Math.max(2, current - delta);
        i <= Math.min(last - 1, current + delta);
        i++
    ) {
        pages.push(i);
    }

    if (current - delta > 2) pages.unshift(-1);
    if (current + delta < last - 1) pages.push(-1);

    pages.unshift(1);
    if (last > 1) pages.push(last);

    return pages.filter((v, i, a) => a.indexOf(v) === i);
});

let searchTimeout: ReturnType<typeof setTimeout>;

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 300);
};

const handleFilterChange = () => applyFilters();

const applyFilters = () => {
    const params: any = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all')
        params.is_active = statusFilter.value;

    router.get('/bank-accounts', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    router.get(
        '/bank-accounts',
        {},
        { preserveState: true, preserveScroll: true },
    );
};

const goToPage = (page: number) => {
    const params: any = { page };
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all')
        params.is_active = statusFilter.value;

    router.get('/bank-accounts', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleCreate = () => router.get('/bank-accounts/create');
</script>
