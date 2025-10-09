<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="`Proposta ${proposal.number}`"
                :description="`Cliente: ${proposal.client?.name}`"
            >
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack">
                        <ArrowLeftIcon class="mr-2 h-4 w-4" />
                        Voltar
                    </Button>
                    <Button variant="outline" @click="downloadPdf">
                        <FileTextIcon class="mr-2 h-4 w-4" />
                        Download PDF
                    </Button>
                    <Button @click="handleEdit">
                        <PencilIcon class="mr-2 h-4 w-4" />
                        Editar
                    </Button>
                    <Button
                        v-if="proposal.status === 'closed'"
                        @click="handleConvertToOrder"
                    >
                        <ShoppingCartIcon class="mr-2 h-4 w-4" />
                        Converter em Encomenda
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2Icon class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Coluna Principal (2/3) -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Informações Principais -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Detalhes da Proposta</CardTitle>
                                <Badge
                                    :variant="
                                        proposal.status === 'closed'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        proposal.status === 'draft'
                                            ? 'Rascunho'
                                            : 'Fechado'
                                    }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Número -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">
                                    Número
                                </div>
                                <div class="col-span-2 font-medium">
                                    {{ proposal.number }}
                                </div>
                            </div>

                            <Separator />

                            <!-- Data -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">
                                    Data da Proposta
                                </div>
                                <div class="col-span-2">
                                    {{ formatDate(proposal.proposal_date) }}
                                </div>
                            </div>

                            <Separator />

                            <!-- Validade -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">
                                    Validade
                                </div>
                                <div class="col-span-2">
                                    {{ formatDate(proposal.validity_date) }}
                                </div>
                            </div>

                            <Separator />

                            <!-- Cliente -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">
                                    Cliente
                                </div>
                                <div class="col-span-2">
                                    <a
                                        v-if="proposal.client"
                                        :href="`/entities/${proposal.client.id}`"
                                        class="text-primary hover:underline"
                                    >
                                        {{ proposal.client.name }}
                                    </a>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Artigos -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Artigos</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div
                                    v-for="(item, index) in proposal.items"
                                    :key="item.id"
                                    class="rounded-lg border p-4"
                                >
                                    <div
                                        class="flex items-start justify-between mb-2"
                                    >
                                        <div>
                                            <div class="font-medium">
                                                {{ item.article?.reference }} -
                                                {{ item.article?.name }}
                                            </div>
                                            <div
                                                v-if="item.supplier"
                                                class="text-sm text-muted-foreground"
                                            >
                                                Fornecedor:
                                                {{ item.supplier.name }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium">
                                                {{
                                                    formatCurrency(
                                                        item.quantity *
                                                            item.unit_price,
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-muted-foreground"
                                                >Qtd:</span
                                            >
                                            {{ item.quantity }}
                                        </div>
                                        <div>
                                            <span class="text-muted-foreground"
                                                >Preço:</span
                                            >
                                            {{ formatCurrency(item.unit_price) }}
                                        </div>
                                        <div v-if="item.cost_price">
                                            <span class="text-muted-foreground"
                                                >Custo:</span
                                            >
                                            {{ formatCurrency(item.cost_price) }}
                                        </div>
                                    </div>

                                    <div
                                        v-if="item.notes"
                                        class="mt-2 text-sm text-muted-foreground"
                                    >
                                        {{ item.notes }}
                                    </div>
                                </div>
                            </div>

                            <!-- Total -->
                            <Separator class="my-4" />
                            <div class="text-right">
                                <div class="text-lg">
                                    <span class="text-muted-foreground"
                                        >Total:</span
                                    >
                                    <span class="ml-4 font-bold text-2xl">{{
                                        formatCurrency(proposal.total_amount)
                                    }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Coluna Lateral (1/3) -->
                <div class="space-y-6">
                    <!-- Informações Adicionais -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informações</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Status -->
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-muted-foreground">
                                    Estado
                                </div>
                                <Badge
                                    :variant="
                                        proposal.status === 'closed'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        proposal.status === 'draft'
                                            ? 'Rascunho'
                                            : 'Fechado'
                                    }}
                                </Badge>
                            </div>

                            <Separator />

                            <!-- Total de Itens -->
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-muted-foreground">
                                    Total de Itens
                                </div>
                                <div class="font-medium">
                                    {{ proposal.items?.length || 0 }}
                                </div>
                            </div>

                            <Separator />

                            <!-- Data de Criação -->
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">
                                    Criado em
                                </div>
                                <div class="text-sm">
                                    {{ formatDateTime(proposal.created_at) }}
                                </div>
                            </div>

                            <Separator />

                            <!-- Data de Atualização -->
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">
                                    Última atualização
                                </div>
                                <div class="text-sm">
                                    {{ formatDateTime(proposal.updated_at) }}
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Ações Rápidas -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Ações</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2">
                            <Button
                                variant="outline"
                                class="w-full justify-start"
                                @click="downloadPdf"
                            >
                                <FileTextIcon class="mr-2 h-4 w-4" />
                                Download PDF
                            </Button>
                            <Button
                                v-if="proposal.status === 'closed'"
                                variant="outline"
                                class="w-full justify-start"
                                @click="handleConvertToOrder"
                            >
                                <ShoppingCartIcon class="mr-2 h-4 w-4" />
                                Converter em Encomenda
                            </Button>
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
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    FileTextIcon,
    PencilIcon,
    ShoppingCartIcon,
    Trash2Icon,
} from 'lucide-vue-next';

interface ProposalItem {
    id: number;
    article_id: number;
    article?: {
        id: number;
        reference: string;
        name: string;
    };
    supplier_id: number | null;
    supplier?: {
        id: number;
        name: string;
    };
    quantity: number;
    unit_price: number;
    cost_price: number | null;
    notes: string | null;
}

interface Proposal {
    id: number;
    number: string;
    proposal_date: string;
    client_id: number;
    client?: {
        id: number;
        name: string;
    };
    validity_date: string;
    total_amount: number;
    status: 'draft' | 'closed';
    items: ProposalItem[];
    created_at: string;
    updated_at: string;
}

interface Props {
    proposal: Proposal;
}

const props = defineProps<Props>();

// Methods
const goBack = () => {
    router.get('/proposals');
};

const handleEdit = () => {
    router.get(`/proposals/${props.proposal.id}/edit`);
};

const handleDelete = () => {
    if (
        confirm(
            `Tem certeza que deseja eliminar a proposta "${props.proposal.number}"?\n\nEsta ação não pode ser desfeita.`
        )
    ) {
        router.delete(`/proposals/${props.proposal.id}`, {
            onSuccess: () => {
                router.get('/proposals');
            },
        });
    }
};

const handleConvertToOrder = () => {
    if (
        confirm(
            `Converter a proposta "${props.proposal.number}" em encomenda?\n\nSerá criada uma encomenda em estado de rascunho.`
        )
    ) {
        router.post(
            `/proposals/${props.proposal.id}/convert-to-order`,
            {},
            {
                preserveScroll: true,
            }
        );
    }
};

const downloadPdf = () => {
    window.open(`/proposals/${props.proposal.id}/pdf`, '_blank');
};

const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(date);
};

const formatDateTime = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>


