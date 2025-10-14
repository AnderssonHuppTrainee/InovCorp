<template>
    <Head title="Encomendas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                title="Encomendas"
                description="Gerir encomendas de clientes"
            >
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Nova Encomenda
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex flex-1 gap-2">
                            <!-- Busca -->
                            <div class="relative max-w-sm flex-1">
                                <SearchIcon
                                    class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground"
                                />
                                <Input
                                    type="search"
                                    placeholder="Buscar por número..."
                                    class="pl-8"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                />
                            </div>

                            <!-- Filtro de Status -->
                            <Select
                                v-model="statusFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[150px]">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="draft"
                                        >Rascunho</SelectItem
                                    >
                                    <SelectItem value="closed"
                                        >Fechado</SelectItem
                                    >
                                </SelectContent>
                            </Select>

                            <!-- Filtro de Cliente -->
                            <Select
                                v-model="clientFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[200px]">
                                    <SelectValue placeholder="Cliente" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all"
                                        >Todos os clientes</SelectItem
                                    >
                                    <SelectItem
                                        v-for="client in clients"
                                        :key="client.id"
                                        :value="String(client.id)"
                                    >
                                        {{ client.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <!-- Botão Limpar Filtros -->
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
                    <!-- Tabela -->
                    <DataTable :columns="columns" :data="orders.data" />

                    <!-- Paginação -->
                    <div
                        class="flex items-center justify-between px-2 py-4"
                        v-if="orders.data.length > 0"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando
                            <strong>{{ orders.from }}</strong>
                            a
                            <strong>{{ orders.to }}</strong>
                            de
                            <strong>{{ orders.total }}</strong>
                            resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!orders.prev_page_url"
                                @click="goToPage(orders.current_page - 1)"
                            >
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>

                            <div class="flex items-center gap-1">
                                <Button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    :variant="
                                        page === orders.current_page
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
                                :disabled="!orders.next_page_url"
                                @click="goToPage(orders.current_page + 1)"
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

interface Client {
    id: number;
    name: string;
}

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
    orders: PaginatedData<any>;
    filters: {
        search?: string;
        status?: string;
        client_id?: string;
    };
    clients: Client[];
}

const props = defineProps<Props>();

// Toast
const { showSuccess, showInfo, showError, showWarning, showLoading } =
    useToast();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Encomendas',
        href: '/orders',
    },
];

// Filtros
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const clientFilter = ref(props.filters.client_id || 'all');

// Computed
const hasFilters = computed(() => {
    return (
        searchQuery.value !== '' ||
        statusFilter.value !== 'all' ||
        clientFilter.value !== 'all'
    );
});

const visiblePages = computed(() => {
    const current = props.orders.current_page;
    const last = props.orders.last_page;
    const delta = 2;
    const pages: number[] = [];

    for (
        let i = Math.max(2, current - delta);
        i <= Math.min(last - 1, current + delta);
        i++
    ) {
        pages.push(i);
    }

    if (current - delta > 2) {
        pages.unshift(-1);
    }
    if (current + delta < last - 1) {
        pages.push(-1);
    }

    pages.unshift(1);
    if (last > 1) {
        pages.push(last);
    }

    return pages.filter((v, i, a) => a.indexOf(v) === i);
});

// Methods
let searchTimeout: ReturnType<typeof setTimeout>;

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
};

const handleFilterChange = () => {
    applyFilters();
};

const applyFilters = () => {
    const params: any = {};

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    if (statusFilter.value && statusFilter.value !== 'all') {
        params.status = statusFilter.value;
    }

    if (clientFilter.value && clientFilter.value !== 'all') {
        params.client_id = clientFilter.value;
    }

    router.get('/orders', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    clientFilter.value = 'all';

    showInfo('Filtros limpos', 'Todos os filtros foram removidos.');

    router.get(
        '/orders',
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const goToPage = (page: number) => {
    const params: any = { page };

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    if (statusFilter.value && statusFilter.value !== 'all') {
        params.status = statusFilter.value;
    }

    if (clientFilter.value && clientFilter.value !== 'all') {
        params.client_id = clientFilter.value;
    }

    router.get('/orders', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleCreate = () => {
    router.get('/orders/create');
};
</script>
