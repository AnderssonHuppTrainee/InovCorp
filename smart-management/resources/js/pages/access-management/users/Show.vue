<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader :title="user.name" :description="user.email">
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack"><ArrowLeftIcon class="mr-2 h-4 w-4" />Voltar</Button>
                    <Button @click="handleEdit"><PencilIcon class="mr-2 h-4 w-4" />Editar</Button>
                    <Button variant="destructive" @click="handleDelete"><Trash2Icon class="mr-2 h-4 w-4" />Eliminar</Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Informações Pessoais</CardTitle>
                                <Badge :variant="user.is_active ? 'default' : 'secondary'">{{ user.is_active ? 'Ativo' : 'Inativo' }}</Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-3 gap-4"><div class="text-sm text-muted-foreground">Nome</div><div class="col-span-2 font-medium">{{ user.name }}</div></div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4"><div class="text-sm text-muted-foreground">Email</div><div class="col-span-2">{{ user.email }}</div></div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4"><div class="text-sm text-muted-foreground">Telemóvel</div><div class="col-span-2">{{ user.mobile || '-' }}</div></div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Permissões</CardTitle></CardHeader>
                        <CardContent>
                            <div v-if="user.roles.length > 0" class="space-y-2">
                                <div class="flex flex-wrap gap-2">
                                    <Badge v-for="role in user.roles" :key="role.id" variant="outline">{{ role.name }}</Badge>
                                </div>
                                <Separator class="my-4" />
                                <div class="text-sm text-muted-foreground">Permissões Totais: {{ permissions.length }}</div>
                            </div>
                            <p v-else class="text-sm text-muted-foreground">Sem grupos atribuídos</p>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card>
                        <CardHeader><CardTitle>Estatísticas</CardTitle></CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-1"><div class="text-sm text-muted-foreground">Ordens de Trabalho</div><div class="text-2xl font-bold">{{ user.work_orders?.length || 0 }}</div></div>
                            <Separator />
                            <div class="space-y-1"><div class="text-sm text-muted-foreground">Eventos de Calendário</div><div class="text-2xl font-bold">{{ user.calendar_events?.length || 0 }}</div></div>
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

interface Props {
    user: any;
    permissions: string[];
}

const props = defineProps<Props>();

const goBack = () => router.get('/users');
const handleEdit = () => router.get(`/users/${props.user.id}/edit`);
const handleDelete = () => {
    if (confirm(`Eliminar utilizador "${props.user.name}"?`)) {
        router.delete(`/users/${props.user.id}`, { onSuccess: () => router.get('/users') });
    }
};
</script>





