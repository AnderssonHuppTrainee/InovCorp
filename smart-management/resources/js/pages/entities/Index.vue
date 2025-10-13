<template>
    <Head :title="type === 'client' ? 'Clientes' : 'Fornecedores'" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                :title="type === 'client' ? 'Clientes' : 'Fornecedores'"
                :description="`Gerir ${type === 'client' ? 'clientes' : 'fornecedores'} do sistema`"
            >
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    {{
                        type === 'client'
                            ? 'Novo Cliente'
                            : 'Novo Fornecedor'
                    }}
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex flex-1 gap-2">
                            <!-- Busca -->
                            <div class="relative flex-1 max-w-sm">
                                <SearchIcon
                                    class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"
                                />
                                <Input
                                    type="search"
                                    placeholder="Buscar por nome ou NIF..."
                                    class="pl-8"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                />
                            </div>

                            <!-- Filtro de Status -->
                            <Select v-model="statusFilter" @update:modelValue="handleFilterChange">
                                <SelectTrigger class="w-[150px]">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="active">Ativos</SelectItem>
                                    <SelectItem value="inactive"
                                        >Inativos</SelectItem
                                    >
                                </SelectContent>
                            </Select>

                            <!-- Filtro de País -->
                            <Select
                                v-model="countryFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="País" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos os países</SelectItem>
                                    <SelectItem
                                        v-for="country in countries"
                                        :key="country.id"
                                        :value="String(country.id)"
                                    >
                                        {{ country.name }}
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
                    <DataTable :columns="columns" :data="entities.data" />

                    <!-- Paginação -->
                    <div
                        class="flex items-center justify-between px-2 py-4"
                        v-if="entities.data.length > 0"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando
                            <strong>{{ entities.from }}</strong>
                            a
                            <strong>{{ entities.to }}</strong>
                            de
                            <strong>{{ entities.total }}</strong>
                            resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!entities.prev_page_url"
                                @click="goToPage(entities.current_page - 1)"
                            >
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>

                            <div class="flex items-center gap-1">
                                <Button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    :variant="
                                        page === entities.current_page
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
                                :disabled="!entities.next_page_url"
                                @click="goToPage(entities.current_page + 1)"
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
import route from '@/routes/entities';
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

interface Country {
    id: number;
    name: string;
    code: string;
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
    type: 'client' | 'supplier';
    entities: PaginatedData<any>;
    filters: {
        search?: string;
        status?: string;
        country_id?: string;
    };
    countries: Country[];
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.type === 'client' ? 'Clientes' : 'Fornecedores',
        href: route.index({ type: props.type }).url,
    },
];

// Filtros
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const countryFilter = ref(props.filters.country_id || 'all');

// Computed
const hasFilters = computed(() => {
    return (
        searchQuery.value !== '' ||
        statusFilter.value !== 'all' ||
        countryFilter.value !== 'all'
    );
});

const visiblePages = computed(() => {
    const current = props.entities.current_page;
    const last = props.entities.last_page;
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
        pages.unshift(-1); // Ellipsis
    }
    if (current + delta < last - 1) {
        pages.push(-1); // Ellipsis
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
    const params: any = { type: props.type };

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    if (statusFilter.value && statusFilter.value !== 'all') {
        params.status = statusFilter.value;
    }

    if (countryFilter.value && countryFilter.value !== 'all') {
        params.country_id = countryFilter.value;
    }

    router.get(route.index().url, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    countryFilter.value = 'all';

    router.get(
        route.index().url,
        { type: props.type },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const goToPage = (page: number) => {
    const params: any = { type: props.type, page };

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    if (statusFilter.value && statusFilter.value !== 'all') {
        params.status = statusFilter.value;
    }

    if (countryFilter.value && countryFilter.value !== 'all') {
        params.country_id = countryFilter.value;
    }

    router.get(route.index().url, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleCreate = () => {
    router.get(route.create().url, { type: props.type });
};
</script>
