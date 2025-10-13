<template>
    <Head title="Ordens de Trabalho" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                title="Ordens de Trabalho"
                description="Gerir ordens de trabalho e tarefas"
            >
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Nova Ordem
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex flex-1 gap-2 flex-wrap">
                            <!-- Busca -->
                            <div class="relative flex-1 min-w-[200px]">
                                <SearchIcon
                                    class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"
                                />
                                <Input
                                    type="search"
                                    placeholder="Buscar..."
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
                                    <SelectValue placeholder="Estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="pending">Pendente</SelectItem>
                                    <SelectItem value="in_progress">Em Progresso</SelectItem>
                                    <SelectItem value="completed">Concluído</SelectItem>
                                    <SelectItem value="cancelled">Cancelado</SelectItem>
                                </SelectContent>
                            </Select>

                            <!-- Filtro de Prioridade -->
                            <Select
                                v-model="priorityFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[150px]">
                                    <SelectValue placeholder="Prioridade" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todas</SelectItem>
                                    <SelectItem value="low">Baixa</SelectItem>
                                    <SelectItem value="medium">Média</SelectItem>
                                    <SelectItem value="high">Alta</SelectItem>
                                    <SelectItem value="urgent">Urgente</SelectItem>
                                </SelectContent>
                            </Select>

                            <!-- Filtro de Cliente -->
                            <Select
                                v-model="clientFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Cliente" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem
                                        v-for="client in clients"
                                        :key="client.id"
                                        :value="String(client.id)"
                                    >
                                        {{ client.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <!-- Filtro de Utilizador -->
                            <Select
                                v-model="userFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Utilizador" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="String(user.id)"
                                    >
                                        {{ user.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <!-- Limpar -->
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
                    <DataTable :columns="columns" :data="workOrders.data" />

                    <!-- Paginação -->
                    <div
                        class="flex items-center justify-between px-2 py-4"
                        v-if="workOrders.data.length > 0"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando
                            <strong>{{ workOrders.from }}</strong>
                            a
                            <strong>{{ workOrders.to }}</strong>
                            de
                            <strong>{{ workOrders.total }}</strong>
                            resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!workOrders.prev_page_url"
                                @click="goToPage(workOrders.current_page - 1)"
                            >
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>

                            <div class="flex items-center gap-1">
                                <Button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    :variant="
                                        page === workOrders.current_page
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
                                :disabled="!workOrders.next_page_url"
                                @click="goToPage(workOrders.current_page + 1)"
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
import DataTable from '@/components/ui/data-table/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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

interface User {
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
    workOrders: PaginatedData<any>;
    filters: {
        search?: string;
        status?: string;
        priority?: string;
        client_id?: string;
        assigned_to?: string;
    };
    clients: Client[];
    users: User[];
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ordens de Trabalho',
        href: '/work-orders',
    },
];

// Filtros
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const priorityFilter = ref(props.filters.priority || 'all');
const clientFilter = ref(props.filters.client_id || 'all');
const userFilter = ref(props.filters.assigned_to || 'all');

// Computed
const hasFilters = computed(() => {
    return (
        searchQuery.value !== '' ||
        statusFilter.value !== 'all' ||
        priorityFilter.value !== 'all' ||
        clientFilter.value !== 'all' ||
        userFilter.value !== 'all'
    );
});

const visiblePages = computed(() => {
    const current = props.workOrders.current_page;
    const last = props.workOrders.last_page;
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

    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all')
        params.status = statusFilter.value;
    if (priorityFilter.value && priorityFilter.value !== 'all')
        params.priority = priorityFilter.value;
    if (clientFilter.value && clientFilter.value !== 'all')
        params.client_id = clientFilter.value;
    if (userFilter.value && userFilter.value !== 'all')
        params.assigned_to = userFilter.value;

    router.get('/work-orders', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    priorityFilter.value = 'all';
    clientFilter.value = 'all';
    userFilter.value = 'all';

    router.get('/work-orders', {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const goToPage = (page: number) => {
    const params: any = { page };

    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all')
        params.status = statusFilter.value;
    if (priorityFilter.value && priorityFilter.value !== 'all')
        params.priority = priorityFilter.value;
    if (clientFilter.value && clientFilter.value !== 'all')
        params.client_id = clientFilter.value;
    if (userFilter.value && userFilter.value !== 'all')
        params.assigned_to = userFilter.value;

    router.get('/work-orders', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleCreate = () => {
    router.get('/work-orders/create');
};
</script>





