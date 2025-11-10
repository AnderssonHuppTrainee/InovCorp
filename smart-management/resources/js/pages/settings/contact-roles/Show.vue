<template>
    <ShowWrapper
        :title="contactRole.name"
        :description="contactRole.description || 'Função de contacto'"
        :edit-url="`/contact-roles/${contactRole.id}/edit`"
        :delete-url="`/contact-roles/${contactRole.id}`"
        :back-url="'/contact-roles'"
        :item-name="contactRole.name"
    >
        <template #main-content>
            <Card>
                <CardHeader>
                    <CardTitle>Detalhes da Função</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Nome</div>
                        <div class="col-span-2 font-medium">
                            {{ contactRole.name }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Descrição
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ contactRole.description || 'Sem descrição' }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Estado</div>
                        <div class="col-span-2">
                            <Badge
                                :variant="
                                    contactRole.is_active
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    contactRole.is_active ? 'Ativa' : 'Inativa'
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
                            Contactos Associados
                        </div>
                        <div class="mt-1 text-2xl font-bold">
                            {{ contactsCount }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Criado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(contactRole.created_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Atualizado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(contactRole.updated_at) }}
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
    contactRole: {
        id: number;
        name: string;
        description: string | null;
        is_active: boolean;
        created_at: string;
        updated_at: string;
    };
    contactsCount: number;
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
