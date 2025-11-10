<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="title" />

        <div class="space-y-6 p-4">
            <!-- Header com título, descrição e botão criar -->
            <PageHeader :title="title" :description="description">
                <slot name="header-actions">
                    <Button @click="handleCreate">
                        <PlusIcon class="mr-2 h-4 w-4" />
                        {{ createButtonText }}
                    </Button>
                </slot>
            </PageHeader>

            <!-- Card principal com filtros e tabela -->
            <Card>
                <CardHeader>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex flex-1 gap-2">
                            <!-- Busca global -->
                            <div
                                v-if="searchConfig.enabled"
                                class="relative max-w-sm flex-1"
                            >
                                <SearchIcon
                                    class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground"
                                />
                                <Input
                                    type="search"
                                    :placeholder="searchConfig.placeholder"
                                    class="pl-8"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                />
                            </div>

                            <!-- Filtros dinâmicos -->
                            <template
                                v-for="filter in filtersConfig"
                                :key="filter.key"
                            >
                                <Select
                                    v-model="filterValues[filter.key]"
                                    @update:modelValue="handleFilterChange"
                                >
                                    <SelectTrigger
                                        :class="
                                            filter.widthClass || 'w-[150px]'
                                        "
                                    >
                                        <SelectValue
                                            :placeholder="filter.placeholder"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            :value="filter.allValue || 'all'"
                                        >
                                            {{ filter.allLabel || 'Todos' }}
                                        </SelectItem>
                                        <SelectItem
                                            v-for="option in filter.options"
                                            :key="option.value"
                                            :value="String(option.value)"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </template>

                            <!-- Botão Limpar Filtros -->
                            <Button
                                v-if="hasFilters"
                                variant="ghost"
                                @click="clearFilters"
                            >
                                <XIcon class="mr-2 h-4 w-4" />
                                Limpar
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                <CardContent>
                    <!-- Tabela de dados -->
                    <DataTable :columns="columns" :data="data.data" />

                    <!-- Paginação -->
                    <div
                        v-if="data.data.length > 0"
                        class="flex items-center justify-between px-2 py-4"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando
                            <strong>{{ data.from }}</strong>
                            a
                            <strong>{{ data.to }}</strong>
                            de
                            <strong>{{ data.total }}</strong>
                            resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!data.prev_page_url"
                                @click="goToPage(data.current_page - 1)"
                            >
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>

                            <div class="flex items-center gap-1">
                                <Button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    :variant="
                                        page === data.current_page
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
                                :disabled="!data.next_page_url"
                                @click="goToPage(data.current_page + 1)"
                            >
                                Próxima
                                <ChevronRightIcon class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>

                    <!-- Slot para conteúdo adicional -->
                    <slot name="additional-content" />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    PlusIcon,
    SearchIcon,
    XIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Components
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

// Types
interface FilterConfig {
    key: string;
    placeholder: string;
    allValue?: string;
    allLabel?: string;
    widthClass?: string;
    options: Array<{ value: string | number; label: string }>;
}

interface SearchConfig {
    enabled: boolean;
    placeholder: string;
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
    title: string;
    description?: string;
    columns: any[];
    data: PaginatedData<any>;
    breadcrumbs?: any[];
    createButtonText?: string;
    searchConfig?: SearchConfig;
    filtersConfig?: FilterConfig[];
    baseUrl: string;
    currentFilters?: Record<string, any>;
    onCreate?: () => void;
}

const props = withDefaults(defineProps<Props>(), {
    description: '',
    createButtonText: 'Novo Item',
    searchConfig: () => ({ enabled: true, placeholder: 'Buscar...' }),
    filtersConfig: () => [],
    breadcrumbs: () => [],
    currentFilters: () => ({}),
    onCreate: () => {},
    // Provide a safe default for paginated data to avoid runtime errors
    data: () =>
        ({
            data: [],
            current_page: 1,
            from: 0,
            to: 0,
            total: 0,
            last_page: 1,
            prev_page_url: null,
            next_page_url: null,
        }) as PaginatedData<any>,
});

// Toast
const { showInfo } = useToast();

// State
const searchQuery = ref(props.currentFilters.search || '');
const filterValues = ref<Record<string, string>>({});

// Initialize filter values
props.filtersConfig.forEach((filter) => {
    filterValues.value[filter.key] =
        props.currentFilters[filter.key] || filter.allValue || 'all';
});

// Computed
const hasFilters = computed(() => {
    const hasSearch = props.searchConfig.enabled && searchQuery.value !== '';
    const hasFilterValues = Object.values(filterValues.value).some(
        (value) => value !== 'all' && value !== '',
    );
    return hasSearch || hasFilterValues;
});

const visiblePages = computed(() => {
    const current = props.data.current_page;
    const last = props.data.last_page;
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
    const params: any = { ...props.currentFilters };

    // Add search
    if (props.searchConfig.enabled && searchQuery.value) {
        params.search = searchQuery.value;
    } else if (props.searchConfig.enabled) {
        delete params.search;
    }

    // Add filters
    props.filtersConfig.forEach((filter) => {
        const value = filterValues.value[filter.key];
        if (value && value !== (filter.allValue || 'all')) {
            params[filter.key] = value;
        } else {
            delete params[filter.key];
        }
    });

    router.get(props.baseUrl, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    props.filtersConfig.forEach((filter) => {
        filterValues.value[filter.key] = filter.allValue || 'all';
    });

    showInfo('Filtros limpos', 'Todos os filtros foram removidos.');

    const cleanParams = { ...props.currentFilters };
    delete cleanParams.search;
    props.filtersConfig.forEach((filter) => {
        delete cleanParams[filter.key];
    });

    router.get(props.baseUrl, cleanParams, {
        preserveState: true,
        preserveScroll: true,
    });
};

const goToPage = (page: number) => {
    const params: any = { ...props.currentFilters, page };

    if (props.searchConfig.enabled && searchQuery.value) {
        params.search = searchQuery.value;
    }

    props.filtersConfig.forEach((filter) => {
        const value = filterValues.value[filter.key];
        if (value && value !== (filter.allValue || 'all')) {
            params[filter.key] = value;
        }
    });

    router.get(props.baseUrl, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleCreate = () => {
    props.onCreate();
};
</script>
