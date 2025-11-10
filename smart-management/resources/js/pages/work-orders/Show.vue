<template>
    <ShowWrapper
        :title="workOrder.title"
        :description="`${workOrder.number} - ${workOrder.client.name}`"
        :edit-url="`/work-orders/${workOrder.id}/edit`"
        :delete-url="`/work-orders/${workOrder.id}`"
        :back-url="'/work-orders'"
        :item-name="workOrder.title"
    >
        <template #main-content>
            <Card>
                <CardHeader>
                    <CardTitle>Detalhes da Ordem de Trabalho</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Número</div>
                        <div class="col-span-2 font-mono font-medium">
                            {{ workOrder.number }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Título</div>
                        <div class="col-span-2 font-medium">
                            {{ workOrder.title }}
                        </div>
                    </div>
                    <Separator />
                    <div
                        class="grid grid-cols-3 gap-4"
                        v-if="workOrder.description"
                    >
                        <div class="text-sm text-muted-foreground">
                            Descrição
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ workOrder.description }}
                        </div>
                    </div>
                    <Separator v-if="workOrder.description" />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Cliente</div>
                        <div class="col-span-2 font-medium">
                            {{ workOrder.client.name }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Prioridade
                        </div>
                        <div class="col-span-2">
                            <Badge
                                :variant="
                                    workOrder.priority === 'urgent'
                                        ? 'destructive'
                                        : workOrder.priority === 'high'
                                          ? 'destructive'
                                          : workOrder.priority === 'medium'
                                            ? 'default'
                                            : 'secondary'
                                "
                            >
                                {{
                                    workOrder.priority === 'urgent'
                                        ? 'Urgente'
                                        : workOrder.priority === 'high'
                                          ? 'Alta'
                                          : workOrder.priority === 'medium'
                                            ? 'Média'
                                            : 'Baixa'
                                }}
                            </Badge>
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Estado</div>
                        <div class="col-span-2">
                            <Badge
                                :variant="
                                    workOrder.status === 'completed'
                                        ? 'default'
                                        : workOrder.status === 'in_progress'
                                          ? 'default'
                                          : workOrder.status === 'cancelled'
                                            ? 'destructive'
                                            : 'secondary'
                                "
                            >
                                {{
                                    workOrder.status === 'pending'
                                        ? 'Pendente'
                                        : workOrder.status === 'in_progress'
                                          ? 'Em Progresso'
                                          : workOrder.status === 'completed'
                                            ? 'Concluída'
                                            : 'Cancelada'
                                }}
                            </Badge>
                        </div>
                    </div>
                    <Separator />
                    <div
                        class="grid grid-cols-3 gap-4"
                        v-if="workOrder.assigned_to"
                    >
                        <div class="text-sm text-muted-foreground">
                            Atribuído a
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ workOrder.assigned_user?.name || 'N/A' }}
                        </div>
                    </div>
                    <Separator v-if="workOrder.assigned_to" />
                    <div
                        class="grid grid-cols-3 gap-4"
                        v-if="workOrder.end_date"
                    >
                        <div class="text-sm text-muted-foreground">
                            Data de Vencimento
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(workOrder.end_date) }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
        <template #sidebar>
            <Card>
                <CardHeader>
                    <CardTitle>Informações</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        class="grid grid-cols-3 gap-4"
                        v-if="workOrder.start_date"
                    >
                        <div class="text-sm text-muted-foreground">Início</div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(workOrder.start_date) }}
                        </div>
                    </div>

                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Criado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(workOrder.created_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Atualizado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(workOrder.updated_at) }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
    </ShowWrapper>
</template>

<script setup lang="ts">
import ShowWrapper from '@/components/common/ShowWrapper.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import type { WorkOrder } from '@/types';

interface Props {
    workOrder: WorkOrder;
}

const props = defineProps<Props>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
