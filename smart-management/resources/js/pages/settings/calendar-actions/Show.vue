<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Detalhes da Ação"
                :description="calendarAction.name"
            >
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack">
                        <ArrowLeftIcon class="mr-2 h-4 w-4" />
                        Voltar
                    </Button>
                    <Button variant="outline" @click="handleEdit">
                        <PencilIcon class="mr-2 h-4 w-4" />
                        Editar
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2Icon class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Informações Gerais</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <div
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Nome
                            </div>
                            <div class="mt-1 text-sm">
                                {{ calendarAction.name }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Descrição
                            </div>
                            <div class="mt-1 text-sm">
                                {{ calendarAction.description || '-' }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Estado
                            </div>
                            <div class="mt-1">
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

                <Card>
                    <CardHeader>
                        <CardTitle>Estatísticas</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <div
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Eventos Associados
                            </div>
                            <div class="mt-1 text-2xl font-bold">
                                {{ eventsCount }}
                            </div>
                        </div>

                        <div class="pt-4">
                            <div
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Criado em
                            </div>
                            <div class="mt-1 text-sm">
                                {{ formatDate(calendarAction.created_at) }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Atualizado em
                            </div>
                            <div class="mt-1 text-sm">
                                {{ formatDate(calendarAction.updated_at) }}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import calendarActions from '@/routes/calendar-actions';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, PencilIcon, Trash2Icon } from 'lucide-vue-next';

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

const goBack = () => router.get('/calendar-actions');

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const handleEdit = () => {
    router.visit(
        calendarActions.edit({ calendar_action: props.calendarAction.id }).url,
    );
};

const handleDelete = () => {
    if (confirm('Tem certeza que deseja eliminar esta ação?')) {
        router.delete(
            calendarActions.destroy({
                calendar_action: props.calendarAction.id,
            }).url,
        );
    }
};
</script>
