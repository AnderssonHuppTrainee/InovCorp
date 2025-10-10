<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Utilizadores" description="Gerir utilizadores do sistema">
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Novo Utilizador
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
                                    placeholder="Buscar utilizador..."
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
                                    <SelectItem value="1">Ativos</SelectItem>
                                    <SelectItem value="0">Inativos</SelectItem>
                                </SelectContent>
                            </Select>

                            <Select v-model="roleFilter" @update:modelValue="handleFilterChange">
                                <SelectTrigger class="w-[200px]">
                                    <SelectValue placeholder="Grupo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem v-for="role in roles" :key="role.id" :value="role.name">
                                        {{ role.name }}
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
                    <DataTable :columns="columns" :data="users.data" />

                    <div class="flex items-center justify-between px-2 py-4" v-if="users.data.length > 0">
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ users.from }}</strong> a <strong>{{ users.to }}</strong> de <strong>{{ users.total }}</strong> resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button variant="outline" size="sm" :disabled="!users.prev_page_url" @click="goToPage(users.current_page - 1)">
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>
                            <Button variant="outline" size="sm" :disabled="!users.next_page_url" @click="goToPage(users.current_page + 1)">
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
    users: any;
    filters: { search?: string; is_active?: string; role?: string };
    roles: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.is_active || 'all');
const roleFilter = ref(props.filters.role || 'all');

const hasFilters = computed(() => {
    return searchQuery.value !== '' || statusFilter.value !== 'all' || roleFilter.value !== 'all';
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
    if (roleFilter.value && roleFilter.value !== 'all') params.role = roleFilter.value;

    router.get('/users', params, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    roleFilter.value = 'all';
    router.get('/users', {}, { preserveState: true, preserveScroll: true });
};

const goToPage = (page: number) => {
    const params: any = { page };
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all') params.is_active = statusFilter.value;
    if (roleFilter.value && roleFilter.value !== 'all') params.role = roleFilter.value;

    router.get('/users', params, { preserveState: true, preserveScroll: true });
};

const handleCreate = () => router.get('/users/create');
</script>




