<template>
    <Head title="Logs de Atividade" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                title="Logs de Atividade"
                description="Histórico de ações no sistema"
            >
            </PageHeader>

            <Card>
                <CardContent class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th
                                        class="p-3 text-left text-sm font-medium"
                                    >
                                        Data
                                    </th>
                                    <th
                                        class="p-3 text-left text-sm font-medium"
                                    >
                                        Hora
                                    </th>
                                    <th
                                        class="p-3 text-left text-sm font-medium"
                                    >
                                        Utilizador
                                    </th>
                                    <th
                                        class="p-3 text-left text-sm font-medium"
                                    >
                                        Menu
                                    </th>
                                    <th
                                        class="p-3 text-left text-sm font-medium"
                                    >
                                        Ação
                                    </th>
                                    <th
                                        class="p-3 text-left text-sm font-medium"
                                    >
                                        IP
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="log in logs.data"
                                    :key="log.id"
                                    class="border-b hover:bg-muted/50"
                                >
                                    <td class="p-3 text-sm">
                                        {{ formatDate(log.created_at) }}
                                    </td>
                                    <td class="p-3 text-sm">
                                        {{ formatTime(log.created_at) }}
                                    </td>
                                    <td class="p-3 text-sm">
                                        {{ log.causer?.name || 'Sistema' }}
                                    </td>
                                    <td class="p-3 text-sm">
                                        <Badge variant="outline">{{
                                            log.log_name || '-'
                                        }}</Badge>
                                    </td>
                                    <td class="p-3 text-sm">
                                        {{ log.description }}
                                    </td>
                                    <td class="p-3 font-mono text-sm text-xs">
                                        {{ log.properties?.ip || '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="mt-4 flex items-center justify-between px-2 py-4"
                        v-if="logs.data.length > 0"
                    >
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ logs.from }}</strong> a
                            <strong>{{ logs.to }}</strong> de
                            <strong>{{ logs.total }}</strong> logs
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!logs.prev_page_url"
                                @click="goToPage(logs.current_page - 1)"
                            >
                                <ChevronLeftIcon class="h-4 w-4" />
                                Anterior
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="!logs.next_page_url"
                                @click="goToPage(logs.current_page + 1)"
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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';

interface Props {
    logs: any;
}

const props = defineProps<Props>();
const { showSuccess, showInfo, showError, showWarning, showLoading } =
    useToast();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Logs de Atividade',
        href: '/settings/logs',
    },
];

const goToPage = (page: number) => {
    router.get(
        '/logs',
        { page },
        { preserveState: true, preserveScroll: true },
    );
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-PT');
};

const formatTime = (dateString: string) => {
    return new Date(dateString).toLocaleTimeString('pt-PT', {
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
