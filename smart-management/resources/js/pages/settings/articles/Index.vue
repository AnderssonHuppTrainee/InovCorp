<template>
    <Head title="Artigos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader title="Artigos" description="Gerir catálogo de artigos">
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Novo Artigo
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
                                    placeholder="Buscar artigo..."
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
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="active"
                                        >Ativos</SelectItem
                                    >
                                    <SelectItem value="inactive"
                                        >Inativos</SelectItem
                                    >
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
                    <DataTable :columns="columns" :data="articles.data" />

                    <div
                        class="flex items-center justify-between px-2 py-4"
                        v-if="articles.data.length > 0"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ articles.from }}</strong> a
                            <strong>{{ articles.to }}</strong> de
                            <strong>{{ articles.total }}</strong> resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!articles.prev_page_url"
                                @click="goToPage(articles.current_page - 1)"
                            >
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!articles.next_page_url"
                                @click="goToPage(articles.current_page + 1)"
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

interface Props {
    articles: any;
    filters: { search?: string; status?: string };
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Artigos',
        href: '/settings/articles',
    },
];

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');

const hasFilters = computed(() => {
    return searchQuery.value !== '' || statusFilter.value !== 'all';
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
        params.status = statusFilter.value;

    router.get('/articles', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    router.get('/articles', {}, { preserveState: true, preserveScroll: true });
};

const goToPage = (page: number) => {
    const params: any = { page };
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all')
        params.status = statusFilter.value;

    router.get('/articles', params, {
        preserveState: true,
        preserveScroll: true,
    });
};
const { showSuccess, showInfo, showError, showWarning, showLoading } =
    useToast();

const handleCreate = () => router.get('/articles/create');
</script>
