<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Taxas de IVA" description="Gerir taxas de IVA aplicadas aos artigos">
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Nova Taxa
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
                                    placeholder="Buscar taxa..."
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
                                    <SelectItem value="all">Todas</SelectItem>
                                    <SelectItem value="1">Ativas</SelectItem>
                                    <SelectItem value="0">Inativas</SelectItem>
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
                    <DataTable :columns="columns" :data="taxRates.data" />

                    <div class="flex items-center justify-between px-2 py-4" v-if="taxRates.data.length > 0">
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ taxRates.from }}</strong> a <strong>{{ taxRates.to }}</strong> de <strong>{{ taxRates.total }}</strong> resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button variant="outline" size="sm" :disabled="!taxRates.prev_page_url" @click="goToPage(taxRates.current_page - 1)">
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>
                            <Button variant="outline" size="sm" :disabled="!taxRates.next_page_url" @click="goToPage(taxRates.current_page + 1)">
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
import { router } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon, PlusIcon, SearchIcon, XIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { columns } from './columns';

interface Props {
    taxRates: any;
    filters: { search?: string; is_active?: string };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.is_active || 'all');

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
    if (statusFilter.value && statusFilter.value !== 'all') params.is_active = statusFilter.value;

    router.get('/tax-rates', params, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    router.get('/tax-rates', {}, { preserveState: true, preserveScroll: true });
};

const goToPage = (page: number) => {
    const params: any = { page };
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all') params.is_active = statusFilter.value;

    router.get('/tax-rates', params, { preserveState: true, preserveScroll: true });
};

const handleCreate = () => router.get('/tax-rates/create');
</script>



