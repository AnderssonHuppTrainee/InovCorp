<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Grupos de Permissões" description="Gerir grupos e permissões do sistema">
                <div class="flex gap-2">
                    <Button variant="outline" @click="syncPermissions"><RefreshCwIcon class="mr-2 h-4 w-4" />Sincronizar Permissões</Button>
                    <Button @click="handleCreate"><PlusIcon class="mr-2 h-4 w-4" />Novo Grupo</Button>
                </div>
            </PageHeader>

            <Card>
                <CardHeader>
                    <div class="flex items-center">
                        <div class="relative flex-1 max-w-sm">
                            <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input type="search" placeholder="Buscar grupo..." class="pl-8" v-model="searchQuery" @input="handleSearch" />
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <DataTable :columns="columns" :data="roles.data" />

                    <div class="flex items-center justify-between px-2 py-4" v-if="roles.data.length > 0">
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ roles.from }}</strong> a <strong>{{ roles.to }}</strong> de <strong>{{ roles.total }}</strong> resultados
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button variant="outline" size="sm" :disabled="!roles.prev_page_url" @click="goToPage(roles.current_page - 1)">
                                <ChevronLeftIcon class="h-4 w-4" />Anterior
                            </Button>
                            <Button variant="outline" size="sm" :disabled="!roles.next_page_url" @click="goToPage(roles.current_page + 1)">
                                Próxima<ChevronRightIcon class="h-4 w-4" />
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
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon, PlusIcon, RefreshCwIcon, SearchIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import { columns } from './columns';

interface Props {
    roles: any;
    filters: { search?: string };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');

let searchTimeout: ReturnType<typeof setTimeout>;

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const params: any = {};
        if (searchQuery.value) params.search = searchQuery.value;
        router.get('/roles', params, { preserveState: true, preserveScroll: true });
    }, 300);
};

const goToPage = (page: number) => {
    const params: any = { page };
    if (searchQuery.value) params.search = searchQuery.value;
    router.get('/roles', params, { preserveState: true, preserveScroll: true });
};

const handleCreate = () => router.get('/roles/create');

const syncPermissions = () => {
    if (confirm('Sincronizar permissões com as rotas do sistema?')) {
        router.post('/roles/sync-permissions', {}, { preserveScroll: true });
    }
};
</script>





