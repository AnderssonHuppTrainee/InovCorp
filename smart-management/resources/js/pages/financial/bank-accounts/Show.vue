<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="`Conta: ${account.name}`"
                :description="`${account.bank_name}`"
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
                                <CardTitle>Informações da Conta</CardTitle>
                                <Badge :variant="account.is_active ? 'default' : 'secondary'">
                                    {{ account.is_active ? 'Ativa' : 'Inativa' }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Nome</div>
                                <div class="col-span-2 font-medium">{{ account.name }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Banco</div>
                                <div class="col-span-2">{{ account.bank_name }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Número da Conta</div>
                                <div class="col-span-2 font-mono">{{ account.account_number }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4" v-if="account.iban">
                                <div class="text-sm text-muted-foreground">IBAN</div>
                                <div class="col-span-2 font-mono">{{ account.iban }}</div>
                            </div>
                            <Separator v-if="account.iban" />
                            <div class="grid grid-cols-3 gap-4" v-if="account.swift">
                                <div class="text-sm text-muted-foreground">SWIFT/BIC</div>
                                <div class="col-span-2 font-mono">{{ account.swift }}</div>
                            </div>
                            <Separator v-if="account.swift" />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Moeda</div>
                                <div class="col-span-2">{{ account.currency }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Saldo</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold" :class="account.balance < 0 ? 'text-destructive' : ''">
                                {{ formatCurrency(account.balance, account.currency) }}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Detalhes</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Criado em</div>
                                <div class="text-sm">{{ formatDateTime(account.created_at) }}</div>
                            </div>
                            <Separator />
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Atualizado</div>
                                <div class="text-sm">{{ formatDateTime(account.updated_at) }}</div>
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

interface Props {
    account: any;
}

const props = defineProps<Props>();

const goBack = () => router.get('/bank-accounts');
const handleEdit = () => router.get(`/bank-accounts/${props.account.id}/edit`);

const handleDelete = () => {
    if (confirm(`Eliminar conta "${props.account.name}"?`)) {
        router.delete(`/bank-accounts/${props.account.id}`, {
            onSuccess: () => router.get('/bank-accounts'),
        });
    }
};

const formatCurrency = (value: number, currency: string) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: currency || 'EUR',
    }).format(value);
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







