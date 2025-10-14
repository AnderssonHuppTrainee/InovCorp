<template>
    <Head title="Ações de Calendário" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                title="Ações de Calendário"
                description="Gerir ações disponíveis para eventos"
            >
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Nova Ação
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
                                    placeholder="Buscar ação..."
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
                                    <SelectItem value="active"
                                        >Ativas</SelectItem
                                    >
                                    <SelectItem value="inactive"
                                        >Inativas</SelectItem
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
                    <DataTable
                        :columns="columns"
                        :data="calendarActionsData.data"
                    />

                    <div
                        class="flex items-center justify-between px-2 py-4"
                        v-if="calendarActionsData.data.length > 0"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando
                            <strong>{{ calendarActionsData.from }}</strong> a
                            <strong>{{ calendarActionsData.to }}</strong> de
                            <strong>{{ calendarActionsData.total }}</strong>
                            resultados
                        </div>
                        <div class="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="handlePreviousPage"
                                :disabled="!calendarActionsData.prev_page_url"
                            >
                                Anterior
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="handleNextPage"
                                :disabled="!calendarActionsData.next_page_url"
                            >
                                Próxima
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
import calendarActions from '@/routes/calendar-actions';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { PlusIcon, SearchIcon, XIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { columns } from './columns';

interface Props {
    calendarActionsData: {
        data: Array<any>;
        current_page: number;
        total: number;
        from: number;
        to: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();

const { showSuccess, showInfo, showError, showWarning, showLoading } =
    useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ações de Calendário',
        href: '/settings/calendar-actions',
    },
];

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');

const hasFilters = computed(() => {
    return searchQuery.value !== '' || statusFilter.value !== 'all';
});

const handleSearch = () => {
    router.get(
        calendarActions.index().url,
        {
            search: searchQuery.value,
            status:
                statusFilter.value !== 'all' ? statusFilter.value : undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const handleFilterChange = () => {
    handleSearch();
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    router.get(
        calendarActions.index().url,
        {},
        { preserveState: true, replace: true },
    );
};

const handleCreate = () => {
    router.visit(calendarActions.create().url);
};

const handlePreviousPage = () => {
    if (props.calendarActionsData.prev_page_url) {
        router.visit(props.calendarActionsData.prev_page_url);
    }
};

const handleNextPage = () => {
    if (props.calendarActionsData.next_page_url) {
        router.visit(props.calendarActionsData.next_page_url);
    }
};
</script>
