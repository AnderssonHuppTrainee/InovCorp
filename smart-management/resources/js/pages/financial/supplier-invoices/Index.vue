<template>
    <Head title="Faturas de Fornecedores" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                title="Faturas de Fornecedores"
                description="Gerir faturas e pagamentos a fornecedores"
            >
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Nova Fatura
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex flex-1 gap-2">
                            <div class="relative flex-1 max-w-sm">
                                <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input
                                    type="search"
                                    placeholder="Buscar por número..."
                                    class="pl-8"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                />
                            </div>

                            <Select v-model="statusFilter" @update:modelValue="handleFilterChange">
                                <SelectTrigger class="w-[150px]">
                                    <SelectValue placeholder="Estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="pending_payment">Pendente</SelectItem>
                                    <SelectItem value="paid">Paga</SelectItem>
                                </SelectContent>
                            </Select>

                            <Select v-model="supplierFilter" @update:modelValue="handleFilterChange">
                                <SelectTrigger class="w-[200px]">
                                    <SelectValue placeholder="Fornecedor" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem v-for="supplier in suppliers" :key="supplier.id" :value="String(supplier.id)">
                                        {{ supplier.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Button variant="ghost" @click="clearFilters" v-if="hasFilters">
                                <XIcon class="mr-2 h-4 w-4" />
                                Limpar
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <DataTable :columns="columns" :data="invoices.data" />

                    <div class="flex items-center justify-between px-2 py-4" v-if="invoices.data.length > 0">
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ invoices.from }}</strong> a <strong>{{ invoices.to }}</strong> de <strong>{{ invoices.total }}</strong> resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button variant="outline" size="sm" :disabled="!invoices.prev_page_url" @click="goToPage(invoices.current_page - 1)">
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>

                            <div class="flex items-center gap-1">
                                <Button v-for="page in visiblePages" :key="page" :variant="page === invoices.current_page ? 'default' : 'outline'" size="sm" @click="goToPage(page)" class="w-9">
                                    {{ page }}
                                </Button>
                            </div>

                            <Button variant="outline" size="sm" :disabled="!invoices.next_page_url" @click="goToPage(invoices.current_page + 1)">
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
import DataTable from '@/components/ui/data-table/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon, PlusIcon, SearchIcon, XIcon } from 'lucide-vue-next';
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
    invoices: PaginatedData<any>;
    filters: { search?: string; status?: string; supplier_id?: string };
    suppliers: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Faturas de Fornecedores',
        href: '/supplier-invoices',
    },
];

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const supplierFilter = ref(props.filters.supplier_id || 'all');

const hasFilters = computed(() => {
    return searchQuery.value !== '' || statusFilter.value !== 'all' || supplierFilter.value !== 'all';
});

const visiblePages = computed(() => {
    const current = props.invoices.current_page;
    const last = props.invoices.last_page;
    const delta = 2;
    const pages: number[] = [];

    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
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
    if (statusFilter.value && statusFilter.value !== 'all') params.status = statusFilter.value;
    if (supplierFilter.value && supplierFilter.value !== 'all') params.supplier_id = supplierFilter.value;

    router.get('/supplier-invoices', params, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    supplierFilter.value = 'all';
    router.get('/supplier-invoices', {}, { preserveState: true, preserveScroll: true });
};

const goToPage = (page: number) => {
    const params: any = { page };
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all') params.status = statusFilter.value;
    if (supplierFilter.value && supplierFilter.value !== 'all') params.supplier_id = supplierFilter.value;

    router.get('/supplier-invoices', params, { preserveState: true, preserveScroll: true });
};

const handleCreate = () => router.get('/supplier-invoices/create');
</script>





