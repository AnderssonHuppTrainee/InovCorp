<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader :title="taxRate.name" :description="`Taxa: ${taxRate.rate}%`">
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
                                <CardTitle>Detalhes da Taxa</CardTitle>
                                <Badge :variant="taxRate.is_active ? 'default' : 'secondary'">
                                    {{ taxRate.is_active ? 'Ativa' : 'Inativa' }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Nome</div>
                                <div class="col-span-2 font-medium">{{ taxRate.name }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Taxa</div>
                                <div class="col-span-2 text-3xl font-bold">{{ taxRate.rate }}%</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Exemplo</div>
                                <div class="col-span-2 text-sm">
                                    <div class="space-y-1">
                                        <div>Produto: 100,00 €</div>
                                        <div>IVA ({{ taxRate.rate }}%): {{ calculateTax(100) }} €</div>
                                        <div class="font-bold">Total: {{ calculateTotal(100) }} €</div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Utilização</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Artigos com esta taxa</div>
                                <div class="text-3xl font-bold">{{ taxRate.articles_count || 0 }}</div>
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
    taxRate: any;
}

const props = defineProps<Props>();

const goBack = () => router.get('/tax-rates');
const handleEdit = () => router.get(`/tax-rates/${props.taxRate.id}/edit`);

const handleDelete = () => {
    if (confirm(`Eliminar taxa "${props.taxRate.name}"?`)) {
        router.delete(`/tax-rates/${props.taxRate.id}`, {
            onSuccess: () => router.get('/tax-rates'),
        });
    }
};

const calculateTax = (value: number) => {
    return (value * props.taxRate.rate / 100).toFixed(2);
};

const calculateTotal = (value: number) => {
    return (value * (1 + props.taxRate.rate / 100)).toFixed(2);
};
</script>



