<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Arquivo Digital"
                description="Gerir documentos e ficheiros"
            >
                <Button @click="handleCreate">
                    <UploadIcon class="mr-2 h-4 w-4" />
                    Enviar Ficheiro
                </Button>
            </PageHeader>

            <!-- Cards de Estatísticas -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">
                            Total Ficheiros
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">
                            Espaço Utilizado
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatBytes(stats.total_size) }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">
                            Públicos
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.public }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">
                            Privados
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.private }}</div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex flex-1 gap-2">
                            <div class="relative flex-1 max-w-sm">
                                <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input
                                    type="search"
                                    placeholder="Buscar ficheiros..."
                                    class="pl-8"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                />
                            </div>

                            <Select v-model="documentTypeFilter" @update:modelValue="handleFilterChange">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Tipo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem v-for="type in documentTypes" :key="type" :value="type">
                                        {{ type }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Select v-model="visibilityFilter" @update:modelValue="handleFilterChange">
                                <SelectTrigger class="w-[150px]">
                                    <SelectValue placeholder="Visibilidade" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todas</SelectItem>
                                    <SelectItem value="1">Públicas</SelectItem>
                                    <SelectItem value="0">Privadas</SelectItem>
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
                    <DataTable :columns="columns" :data="archives.data" />

                    <div class="flex items-center justify-between px-2 py-4" v-if="archives.data.length > 0">
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ archives.from }}</strong> a <strong>{{ archives.to }}</strong> de <strong>{{ archives.total }}</strong> resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button variant="outline" size="sm" :disabled="!archives.prev_page_url" @click="goToPage(archives.current_page - 1)">
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>

                            <Button variant="outline" size="sm" :disabled="!archives.next_page_url" @click="goToPage(archives.current_page + 1)">
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
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon, SearchIcon, UploadIcon, XIcon } from 'lucide-vue-next';
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
    archives: PaginatedData<any>;
    filters: { search?: string; document_type?: string; is_public?: string };
    documentTypes: string[];
    archivableTypes: string[];
    stats: { total: number; total_size: number; public: number; private: number };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
const documentTypeFilter = ref(props.filters.document_type || 'all');
const visibilityFilter = ref(props.filters.is_public || 'all');

const hasFilters = computed(() => {
    return searchQuery.value !== '' || documentTypeFilter.value !== 'all' || visibilityFilter.value !== 'all';
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
    if (documentTypeFilter.value && documentTypeFilter.value !== 'all') params.document_type = documentTypeFilter.value;
    if (visibilityFilter.value && visibilityFilter.value !== 'all') params.is_public = visibilityFilter.value;

    router.get('/digital-archive', params, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    documentTypeFilter.value = 'all';
    visibilityFilter.value = 'all';
    router.get('/digital-archive', {}, { preserveState: true, preserveScroll: true });
};

const goToPage = (page: number) => {
    const params: any = { page };
    if (searchQuery.value) params.search = searchQuery.value;
    if (documentTypeFilter.value && documentTypeFilter.value !== 'all') params.document_type = documentTypeFilter.value;
    if (visibilityFilter.value && visibilityFilter.value !== 'all') params.is_public = visibilityFilter.value;

    router.get('/digital-archive', params, { preserveState: true, preserveScroll: true });
};

const handleCreate = () => router.get('/digital-archive/create');

const formatBytes = (bytes: number) => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let i = 0;
    
    while (size > 1024 && i < units.length - 1) {
        size /= 1024;
        i++;
    }
    
    return `${size.toFixed(2)} ${units[i]}`;
};
</script>




