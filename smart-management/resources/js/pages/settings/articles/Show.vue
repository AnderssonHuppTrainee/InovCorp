<template>
    <ShowWrapper
        :title="article.name"
        :description="`Ref: ${article.reference}`"
        :edit-url="`/articles/${article.id}/edit`"
        :delete-url="`/articles/${article.id}`"
        :back-url="'/articles'"
        :item-name="article.name"
    >
        <template #main-content>
            <Card>
                <CardHeader>
                    <CardTitle>Detalhes do Artigo</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Referência
                        </div>
                        <div class="col-span-2 font-mono font-medium">
                            {{ article.reference }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Nome</div>
                        <div class="col-span-2 font-medium">
                            {{ article.name }}
                        </div>
                    </div>
                    <Separator />
                    <div
                        class="grid grid-cols-3 gap-4"
                        v-if="article.description"
                    >
                        <div class="text-sm text-muted-foreground">
                            Descrição
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ article.description }}
                        </div>
                    </div>
                    <Separator v-if="article.description" />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Preço s/ IVA
                        </div>
                        <div class="col-span-2 font-bold">
                            {{ formatCurrency(article.price) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">IVA</div>
                        <div class="col-span-2">
                            <Badge
                                >{{ article.tax_rate.name }} ({{
                                    article.tax_rate.rate
                                }}%)</Badge
                            >
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Preço c/ IVA
                        </div>
                        <div class="col-span-2 text-lg font-bold">
                            {{
                                formatCurrency(
                                    article.price *
                                        (1 + article.tax_rate.rate / 100),
                                )
                            }}
                        </div>
                    </div>
                    <Separator v-if="article.observations" />
                    <div
                        class="grid grid-cols-3 gap-4"
                        v-if="article.observations"
                    >
                        <div class="text-sm text-muted-foreground">
                            Observações
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ article.observations }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>

        <template #sidebar>
            <Card v-if="article.photo">
                <CardHeader><CardTitle>Foto</CardTitle></CardHeader>
                <CardContent>
                    <img
                        :src="`/storage/${article.photo}`"
                        :alt="article.name"
                        class="w-full rounded-lg object-cover"
                    />
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Estado</CardTitle></CardHeader>
                <CardContent>
                    <Badge
                        :variant="
                            article.status === 'active'
                                ? 'default'
                                : 'secondary'
                        "
                        class="px-4 py-2 text-sm"
                    >
                        {{ article.status === 'active' ? 'Ativo' : 'Inativo' }}
                    </Badge>
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
    article: any;
}

const props = defineProps<Props>();

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>
