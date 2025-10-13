<template>
    <Head title="Contactos" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                title="Contactos"
                description="Gerir contactos das entidades"
            >
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Novo Contacto
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
                                    placeholder="Buscar por nome, email ou telefone..."
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
                                    <SelectItem value="active">Ativos</SelectItem>
                                    <SelectItem value="inactive"
                                        >Inativos</SelectItem
                                    >
                                </SelectContent>
                            </Select>

                            <!-- Filtro de Entidade -->
                            <Select
                                v-model="entityFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Entidade" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all"
                                        >Todas as entidades</SelectItem
                                    >
                                    <SelectItem
                                        v-for="entity in entities"
                                        :key="entity.id"
                                        :value="String(entity.id)"
                                    >
                                        {{ entity.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <!-- Filtro de Função -->
                            <Select
                                v-model="roleFilter"
                                @update:modelValue="handleFilterChange"
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Função" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all"
                                        >Todas as funções</SelectItem
                                    >
                                    <SelectItem
                                        v-for="role in roles"
                                        :key="role.id"
                                        :value="String(role.id)"
                                    >
                                        {{ role.name }}
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
                    <DataTable :columns="columns" :data="contacts.data" />

                    <!-- Paginação -->
                    <div
                        class="flex items-center justify-between px-2 py-4"
                        v-if="contacts.data.length > 0"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando
                            <strong>{{ contacts.from }}</strong>
                            a
                            <strong>{{ contacts.to }}</strong>
                            de
                            <strong>{{ contacts.total }}</strong>
                            resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!contacts.prev_page_url"
                                @click="goToPage(contacts.current_page - 1)"
                            >
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>

                            <div class="flex items-center gap-1">
                                <Button
                                    v-for="page in visiblePages"
                                    :key="page"
                                    :variant="
                                        page === contacts.current_page
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
                                :disabled="!contacts.next_page_url"
                                @click="goToPage(contacts.current_page + 1)"
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

interface Entity {
    id: number;
    name: string;
}

interface Role {
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
    contacts: PaginatedData<any>;
    filters: {
        search?: string;
        status?: string;
        entity_id?: string;
        contact_role_id?: string;
    };
    entities: Entity[];
    roles: Role[];
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contactos',
        href: '/contacts',
    },
];

// Filtros
const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const entityFilter = ref(props.filters.entity_id || 'all');
const roleFilter = ref(props.filters.contact_role_id || 'all');

// Computed
const hasFilters = computed(() => {
    return (
        searchQuery.value !== '' ||
        statusFilter.value !== 'all' ||
        entityFilter.value !== 'all' ||
        roleFilter.value !== 'all'
    );
});

const visiblePages = computed(() => {
    const current = props.contacts.current_page;
    const last = props.contacts.last_page;
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

    if (entityFilter.value && entityFilter.value !== 'all') {
        params.entity_id = entityFilter.value;
    }

    if (roleFilter.value && roleFilter.value !== 'all') {
        params.contact_role_id = roleFilter.value;
    }

    router.get('/contacts', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    entityFilter.value = 'all';
    roleFilter.value = 'all';

    router.get('/contacts', {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const goToPage = (page: number) => {
    const params: any = { page };

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    if (statusFilter.value && statusFilter.value !== 'all') {
        params.status = statusFilter.value;
    }

    if (entityFilter.value && entityFilter.value !== 'all') {
        params.entity_id = entityFilter.value;
    }

    if (roleFilter.value && roleFilter.value !== 'all') {
        params.contact_role_id = roleFilter.value;
    }

    router.get('/contacts', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleCreate = () => {
    router.get('/contacts/create');
};
</script>


