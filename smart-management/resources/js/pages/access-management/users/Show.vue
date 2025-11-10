<template>
    <ShowWrapper
        :title="user.name"
        :description="user.email"
        :edit-url="`/users/${user.id}/edit`"
        :delete-url="`/users/${user.id}`"
        :back-url="'/users'"
        :item-name="user.name"
    >
        <template #main-content>
            <Card>
                <CardHeader>
                    <CardTitle>Detalhes do Utilizador</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Nome</div>
                        <div class="col-span-2 font-medium">
                            {{ user.name }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Email</div>
                        <div class="col-span-2 text-sm">
                            <a
                                :href="`mailto:${user.email}`"
                                class="text-blue-600 hover:underline"
                            >
                                {{ user.email }}
                            </a>
                        </div>
                    </div>

                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Estado</div>
                        <div class="col-span-2">
                            <Badge
                                :variant="
                                    user.is_active ? 'default' : 'secondary'
                                "
                            >
                                {{ user.is_active ? 'Ativo' : 'Inativo' }}
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Permissões</CardTitle></CardHeader>
                <CardContent>
                    <div v-if="user.roles.length > 0" class="space-y-2">
                        <div class="text-sm text-muted-foreground">Função</div>
                        <div class="flex flex-wrap gap-2">
                            <Badge
                                v-for="role in user.roles"
                                :key="role.id"
                                variant="outline"
                                >{{ role.name }}</Badge
                            >
                        </div>
                        <Separator class="my-4" />
                        <div class="text-sm text-muted-foreground">
                            Permissões Totais: {{ permissions.length }}
                        </div>
                    </div>
                    <p v-else class="text-sm text-muted-foreground">
                        Sem grupos atribuídos
                    </p>
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
                        <div class="text-sm text-muted-foreground">ID</div>
                        <div class="col-span-2 font-mono text-sm">
                            {{ user.id }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Criado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(user.created_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Atualizado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(user.updated_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <div class="text-sm text-muted-foreground">
                                Ordens de Trabalho
                            </div>
                            <div class="text-2xl font-bold">
                                {{ user.work_orders?.length || 0 }}
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-sm text-muted-foreground">
                                Eventos de Calendário
                            </div>
                            <div class="text-2xl font-bold">
                                {{ user.calendar_events?.length || 0 }}
                            </div>
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

interface Props {
    user: {
        id: number;
        name: string;
        email: string;
        is_active: boolean;
        created_at: string;
        updated_at: string;
        roles: any;
        calendar_events: any;
        work_orders: any;
    };
    permissions: string[];
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
