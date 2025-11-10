<template>
    <ShowWrapper
        :title="calendarAction.name"
        :description="calendarAction.description || 'Ação de calendário'"
        :edit-url="`/calendar-actions/${calendarAction.id}/edit`"
        :delete-url="`/calendar-actions/${calendarAction.id}`"
        :back-url="'/calendar-actions'"
        :item-name="calendarAction.name"
    >
        <template #main-content>
            <Card>
                <CardHeader>
                    <CardTitle>Detalhes da Ação</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Nome</div>
                        <div class="col-span-2 font-medium">
                            {{ calendarAction.name }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Descrição
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ calendarAction.description || 'Sem descrição' }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Estado</div>
                        <div class="col-span-2">
                            <Badge
                                :variant="
                                    calendarAction.is_active
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    calendarAction.is_active
                                        ? 'Ativa'
                                        : 'Inativa'
                                }}
                            </Badge>
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
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">
                            Eventos Associados
                        </div>
                        <div class="mt-1 text-2xl font-bold">
                            {{ eventsCount }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Criado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(calendarAction.created_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Atualizado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(calendarAction.updated_at) }}
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
import { router } from '@inertiajs/vue3';

interface Props {
    calendarAction: {
        id: number;
        name: string;
        description: string | null;
        is_active: boolean;
        created_at: string;
        updated_at: string;
    };
    eventsCount: number;
}

const props = defineProps<Props>();

const handleEdit = () =>
    router.get(`/calendar-actions/${props.calendarAction.id}/edit`);
const handleDelete = () => {
    if (confirm(`Eliminar ação "${props.calendarAction.name}"?`)) {
        router.delete(`/calendar-actions/${props.calendarAction.id}`, {
            onSuccess: () => router.get('/calendar-actions'),
        });
    }
};

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
