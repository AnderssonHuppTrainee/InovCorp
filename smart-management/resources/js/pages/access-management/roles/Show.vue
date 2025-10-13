<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader :title="role.name" description="Detalhes do grupo de permissões">
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack"><ArrowLeftIcon class="mr-2 h-4 w-4" />Voltar</Button>
                    <Button @click="handleEdit"><PencilIcon class="mr-2 h-4 w-4" />Editar</Button>
                    <Button variant="destructive" @click="handleDelete"><Trash2Icon class="mr-2 h-4 w-4" />Eliminar</Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader><CardTitle>Permissões</CardTitle></CardHeader>
                        <CardContent>
                            <div v-if="role.permissions.length > 0" class="space-y-4">
                                <div class="flex flex-wrap gap-2">
                                    <Badge v-for="permission in role.permissions" :key="permission.id" variant="outline" class="text-xs">
                                        {{ permission.name }}
                                    </Badge>
                                </div>
                            </div>
                            <p v-else class="text-sm text-muted-foreground">Sem permissões atribuídas</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Utilizadores</CardTitle></CardHeader>
                        <CardContent>
                            <div v-if="role.users.length > 0" class="space-y-2">
                                <div v-for="user in role.users" :key="user.id" class="flex items-center justify-between p-2 border rounded-md">
                                    <div>
                                        <div class="font-medium">{{ user.name }}</div>
                                        <div class="text-sm text-muted-foreground">{{ user.email }}</div>
                                    </div>
                                    <Button variant="outline" size="sm" @click="router.get(`/users/${user.id}`)">Ver</Button>
                                </div>
                            </div>
                            <p v-else class="text-sm text-muted-foreground">Sem utilizadores neste grupo</p>
                        </CardContent>
                    </Card>
                </div>

                <div>
                    <Card>
                        <CardHeader><CardTitle>Estatísticas</CardTitle></CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-1"><div class="text-sm text-muted-foreground">Permissões</div><div class="text-2xl font-bold">{{ role.permissions.length }}</div></div>
                            <Separator />
                            <div class="space-y-1"><div class="text-sm text-muted-foreground">Utilizadores</div><div class="text-2xl font-bold">{{ role.users.length }}</div></div>
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
    role: any;
}

const props = defineProps<Props>();

const goBack = () => router.get('/roles');
const handleEdit = () => router.get(`/roles/${props.role.id}/edit`);
const handleDelete = () => {
    if (confirm(`Eliminar grupo "${props.role.name}"?`)) {
        router.delete(`/roles/${props.role.id}`, { onSuccess: () => router.get('/roles') });
    }
};
</script>





