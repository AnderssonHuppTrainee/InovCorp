<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="workOrder.title"
                :description="`OT #${workOrder.number}`"
            >
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack">
                        <ArrowLeftIcon class="mr-2 h-4 w-4" />
                        Voltar
                    </Button>
                    <Button @click="handleEdit">
                        <PencilIcon class="mr-2 h-4 w-4" />
                        Editar
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2Icon class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Detalhes</CardTitle>
                                <div class="flex gap-2">
                                    <Badge :variant="priorityVariant">{{ priorityLabel }}</Badge>
                                    <Badge :variant="statusVariant">{{ statusLabel }}</Badge>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Número</div>
                                <div class="col-span-2 font-medium">{{ workOrder.number }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Título</div>
                                <div class="col-span-2 font-medium">{{ workOrder.title }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Cliente</div>
                                <div class="col-span-2">
                                    <a v-if="workOrder.client" :href="`/entities/${workOrder.client.id}`" class="text-primary hover:underline">
                                        {{ workOrder.client.name }}
                                    </a>
                                </div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Atribuído a</div>
                                <div class="col-span-2">{{ workOrder.assigned_user?.name }}</div>
                            </div>
                            <Separator v-if="workOrder.description" />
                            <div class="grid grid-cols-3 gap-4" v-if="workOrder.description">
                                <div class="text-sm text-muted-foreground">Descrição</div>
                                <div class="col-span-2 whitespace-pre-line">{{ workOrder.description }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Datas</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-1" v-if="workOrder.start_date">
                                <div class="text-sm text-muted-foreground">Início</div>
                                <div class="font-medium">{{ formatDate(workOrder.start_date) }}</div>
                            </div>
                            <Separator v-if="workOrder.start_date && workOrder.end_date" />
                            <div class="space-y-1" v-if="workOrder.end_date">
                                <div class="text-sm text-muted-foreground">Fim</div>
                                <div class="font-medium">{{ formatDate(workOrder.end_date) }}</div>
                            </div>
                            <Separator />
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Criado em</div>
                                <div class="text-sm">{{ formatDateTime(workOrder.created_at) }}</div>
                            </div>
                            <Separator />
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Atualizado</div>
                                <div class="text-sm">{{ formatDateTime(workOrder.updated_at) }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, PencilIcon, Trash2Icon } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    workOrder: any;
}

const props = defineProps<Props>();

const priorityVariants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    low: 'secondary',
    medium: 'outline',
    high: 'default',
    urgent: 'destructive',
};

const priorityLabels: Record<string, string> = {
    low: 'Baixa',
    medium: 'Média',
    high: 'Alta',
    urgent: 'Urgente',
};

const statusVariants: Record<string, 'default' | 'secondary' | 'outline' | 'destructive'> = {
    pending: 'secondary',
    in_progress: 'default',
    completed: 'outline',
    cancelled: 'destructive',
};

const statusLabels: Record<string, string> = {
    pending: 'Pendente',
    in_progress: 'Em Progresso',
    completed: 'Concluído',
    cancelled: 'Cancelado',
};

const priorityVariant = computed(() => priorityVariants[props.workOrder.priority] || 'outline');
const priorityLabel = computed(() => priorityLabels[props.workOrder.priority] || props.workOrder.priority);
const statusVariant = computed(() => statusVariants[props.workOrder.status] || 'outline');
const statusLabel = computed(() => statusLabels[props.workOrder.status] || props.workOrder.status);

const goBack = () => router.get('/work-orders');
const handleEdit = () => router.get(`/work-orders/${props.workOrder.id}/edit`);

const handleDelete = () => {
    if (confirm(`Eliminar "${props.workOrder.title}"?`)) {
        router.delete(`/work-orders/${props.workOrder.id}`, {
            onSuccess: () => router.get('/work-orders'),
        });
    }
};

const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatDateTime = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>




