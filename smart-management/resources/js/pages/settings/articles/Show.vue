<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader :title="article.name" :description="`Ref: ${article.reference}`">
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack"><ArrowLeftIcon class="mr-2 h-4 w-4" />Voltar</Button>
                    <Button @click="handleEdit"><PencilIcon class="mr-2 h-4 w-4" />Editar</Button>
                    <Button variant="destructive" @click="handleDelete"><Trash2Icon class="mr-2 h-4 w-4" />Eliminar</Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader><CardTitle>Detalhes do Artigo</CardTitle></CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-3 gap-4"><div class="text-sm text-muted-foreground">Referência</div><div class="col-span-2 font-medium font-mono">{{ article.reference }}</div></div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4"><div class="text-sm text-muted-foreground">Nome</div><div class="col-span-2 font-medium">{{ article.name }}</div></div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4" v-if="article.description"><div class="text-sm text-muted-foreground">Descrição</div><div class="col-span-2 text-sm">{{ article.description }}</div></div>
                            <Separator v-if="article.description" />
                            <div class="grid grid-cols-3 gap-4"><div class="text-sm text-muted-foreground">Preço s/ IVA</div><div class="col-span-2 font-bold">{{ formatCurrency(article.price) }}</div></div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4"><div class="text-sm text-muted-foreground">IVA</div><div class="col-span-2"><Badge>{{ article.tax_rate.name }} ({{ article.tax_rate.rate }}%)</Badge></div></div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4"><div class="text-sm text-muted-foreground">Preço c/ IVA</div><div class="col-span-2 font-bold text-lg">{{ formatCurrency(article.price * (1 + article.tax_rate.rate / 100)) }}</div></div>
                            <Separator v-if="article.observations" />
                            <div class="grid grid-cols-3 gap-4" v-if="article.observations"><div class="text-sm text-muted-foreground">Observações</div><div class="col-span-2 text-sm">{{ article.observations }}</div></div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card v-if="article.photo">
                        <CardHeader><CardTitle>Foto</CardTitle></CardHeader>
                        <CardContent>
                            <img :src="`/storage/${article.photo}`" :alt="article.name" class="w-full rounded-lg object-cover" />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Estado</CardTitle></CardHeader>
                        <CardContent>
                            <Badge :variant="article.status === 'active' ? 'default' : 'secondary'" class="text-lg px-4 py-2">
                                {{ article.status === 'active' ? 'Ativo' : 'Inativo' }}
                            </Badge>
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
    article: any;
}

const props = defineProps<Props>();

const goBack = () => router.get('/articles');
const handleEdit = () => router.get(`/articles/${props.article.id}/edit`);
const handleDelete = () => {
    if (confirm(`Eliminar artigo "${props.article.name}"?`)) {
        router.delete(`/articles/${props.article.id}`, { onSuccess: () => router.get('/articles') });
    }
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>



