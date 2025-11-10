<template>
    <ShowWrapper
        :title="calendarEventType.name"
        :description="calendarEventType.description || 'Tipo de evento'"
        :edit-url="`/calendar-event-types/${calendarEventType.id}/edit`"
        :delete-url="`/calendar-event-types/${calendarEventType.id}`"
        :back-url="'/calendar-event-types'"
        :item-name="calendarEventType.name"
    >
        <template #main-content>
            <Card>
                <CardHeader>
                    <CardTitle>Detalhes do Tipo de Evento</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Nome</div>
                        <div class="col-span-2 font-medium">
                            {{ calendarEventType.name }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Descrição
                        </div>
                        <div class="col-span-2 text-sm">
                            {{
                                calendarEventType.description || 'Sem descrição'
                            }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Cor</div>
                        <div class="col-span-2 flex items-center gap-2">
                            <div
                                class="h-4 w-4 rounded"
                                :style="{
                                    backgroundColor: calendarEventType.color,
                                }"
                            ></div>
                            <span class="font-mono text-sm">{{
                                calendarEventType.color
                            }}</span>
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Estado</div>
                        <div class="col-span-2">
                            <Badge
                                :variant="
                                    calendarEventType.is_active
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    calendarEventType.is_active
                                        ? 'Ativo'
                                        : 'Inativo'
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
                            {{ formatDate(calendarEventType.created_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Atualizado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(calendarEventType.updated_at) }}
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
    calendarEventType: {
        id: number;
        name: string;
        description: string | null;
        color: string;
        is_active: boolean;
        created_at: string;
        updated_at: string;
    };
    eventsCount: number;
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
