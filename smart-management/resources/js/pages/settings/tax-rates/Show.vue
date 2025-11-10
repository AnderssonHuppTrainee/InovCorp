<template>
    <ShowWrapper
        :title="taxRate.name"
        :description="`Taxa: ${taxRate.rate}%`"
        :edit-url="`/tax-rates/${taxRate.id}/edit`"
        :delete-url="`/tax-rates/${taxRate.id}`"
        :back-url="'/tax-rates'"
        :item-name="taxRate.name"
    >
        <template #main-content>
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Detalhes da Taxa</CardTitle>
                        <Badge
                            :variant="
                                taxRate.is_active ? 'default' : 'secondary'
                            "
                        >
                            {{ taxRate.is_active ? 'Ativa' : 'Inativa' }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Nome</div>
                        <div class="col-span-2 font-medium">
                            {{ taxRate.name }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Taxa</div>
                        <div class="col-span-2 text-3xl font-bold">
                            {{ taxRate.rate }}%
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Exemplo</div>
                        <div class="col-span-2 text-sm">
                            <div class="space-y-1">
                                <div>Produto: 100,00 €</div>
                                <div>
                                    IVA ({{ taxRate.rate }}%):
                                    {{ calculateTax(100) }} €
                                </div>
                                <div class="font-bold">
                                    Total: {{ calculateTotal(100) }} €
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
        <template #sidebar>
            <Card>
                <CardHeader>
                    <CardTitle>Utilização</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-1">
                        <div class="text-sm text-muted-foreground">
                            Artigos com esta taxa
                        </div>
                        <div class="text-3xl font-bold">
                            {{ taxRate.articles_count || 0 }}
                        </div>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Criado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(taxRate.created_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Atualizado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(taxRate.updated_at) }}
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
    taxRate: {
        id: number;
        name: string;
        rate: number;
        is_active: boolean;
        created_at: string;
        updated_at: string;
        articles_count: number;
    };
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
const calculateTax = (value: number) => {
    return ((value * props.taxRate.rate) / 100).toFixed(2);
};

const calculateTotal = (value: number) => {
    return (value * (1 + props.taxRate.rate / 100)).toFixed(2);
};
</script>
