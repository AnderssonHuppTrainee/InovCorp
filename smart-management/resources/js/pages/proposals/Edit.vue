<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Editar Proposta"
                :description="`Proposta #${proposal.number}`"
            >
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <form @submit.prevent="submitForm">
                <!-- Informações da Proposta -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle>Informações da Proposta</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <!-- Data da Proposta -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium"
                                    >Data da Proposta *</label
                                >
                                <Input
                                    type="date"
                                    v-model="formData.proposal_date"
                                    required
                                />
                            </div>

                            <!-- Cliente -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium"
                                    >Cliente *</label
                                >
                                <select
                                    v-model="formData.client_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    required
                                >
                                    <option value="">Selecione um cliente</option>
                                    <option
                                        v-for="client in clients"
                                        :key="client.id"
                                        :value="client.id"
                                    >
                                        {{ client.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Validade -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium"
                                    >Validade *</label
                                >
                                <Input
                                    type="date"
                                    v-model="formData.validity_date"
                                    required
                                />
                            </div>

                            <!-- Estado -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Estado *</label>
                                <select
                                    v-model="formData.status"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="draft">Rascunho</option>
                                    <option value="closed">Fechado</option>
                                </select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Linhas de Artigos -->
                <Card class="mb-6">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Artigos</CardTitle>
                            <Button type="button" @click="addItem">
                                <PlusIcon class="mr-2 h-4 w-4" />
                                Adicionar Artigo
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="formData.items.length === 0"
                            class="text-center py-8 text-muted-foreground"
                        >
                            Nenhum artigo adicionado. Clique em "Adicionar Artigo"
                            para começar.
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="(item, index) in formData.items"
                                :key="index"
                                class="rounded-lg border p-4 space-y-4"
                            >
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium">Item {{ index + 1 }}</h4>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        @click="removeItem(index)"
                                    >
                                        <Trash2Icon class="h-4 w-4" />
                                    </Button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <!-- Artigo -->
                                    <div class="md:col-span-2 space-y-2">
                                        <label class="text-sm">Artigo *</label>
                                        <select
                                            v-model="item.article_id"
                                            @change="onArticleChange(index)"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                            required
                                        >
                                            <option value="">Selecione</option>
                                            <option
                                                v-for="article in articles"
                                                :key="article.id"
                                                :value="article.id"
                                            >
                                                {{ article.reference }} -
                                                {{ article.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Fornecedor -->
                                    <div class="space-y-2">
                                        <label class="text-sm">Fornecedor</label>
                                        <select
                                            v-model="item.supplier_id"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        >
                                            <option value="">Sem fornecedor</option>
                                            <option
                                                v-for="supplier in suppliers"
                                                :key="supplier.id"
                                                :value="supplier.id"
                                            >
                                                {{ supplier.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Quantidade -->
                                    <div class="space-y-2">
                                        <label class="text-sm">Quantidade *</label>
                                        <Input
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            v-model.number="item.quantity"
                                            @input="calculateItemTotal(index)"
                                            required
                                        />
                                    </div>

                                    <!-- Preço Unit. -->
                                    <div class="space-y-2">
                                        <label class="text-sm">Preço Unit. *</label>
                                        <Input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            v-model.number="item.unit_price"
                                            @input="calculateItemTotal(index)"
                                            required
                                        />
                                    </div>

                                    <!-- Preço Custo -->
                                    <div class="space-y-2">
                                        <label class="text-sm">Preço Custo</label>
                                        <Input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            :value="item.cost_price ?? ''"
                                            @input="
                                                (e: Event) =>
                                                    (item.cost_price =
                                                        (e.target as HTMLInputElement).value
                                                            ? parseFloat((e.target as HTMLInputElement).value)
                                                            : null)
                                            "
                                        />
                                    </div>
                                </div>

                                <!-- Notas -->
                                <div class="space-y-2">
                                    <label class="text-sm">Notas</label>
                                    <Textarea
                                        v-model="item.notes"
                                        placeholder="Notas sobre este item"
                                        rows="2"
                                    />
                                </div>

                                <!-- Total da Linha -->
                                <div class="text-right">
                                    <span class="text-sm text-muted-foreground"
                                        >Subtotal:</span
                                    >
                                    <span class="ml-2 font-medium">{{
                                        formatCurrency(
                                            item.quantity * item.unit_price || 0,
                                        )
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Total Geral -->
                        <div
                            v-if="formData.items.length > 0"
                            class="mt-6 border-t pt-4 text-right"
                        >
                            <div class="text-lg">
                                <span class="text-muted-foreground">Total:</span>
                                <span class="ml-4 font-bold text-2xl">{{
                                    formatCurrency(totalAmount)
                                }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Botões de Ação -->
                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack">
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon
                            v-else
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        {{
                            isSubmitting ? 'A atualizar...' : 'Atualizar Proposta'
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    LoaderIcon,
    PlusIcon,
    SaveIcon,
    Trash2Icon,
} from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

interface Article {
    id: number;
    reference: string;
    name: string;
    price: number;
}

interface ProposalItem {
    id?: number;
    article_id: number;
    supplier_id: number | null;
    quantity: number;
    unit_price: number;
    cost_price: number | null;
    notes: string;
}

interface Props {
    proposal: {
        id: number;
        number: string;
        proposal_date: string;
        client_id: number;
        validity_date: string;
        status: 'draft' | 'closed';
        items: ProposalItem[];
    };
    clients: Array<{ id: number; name: string }>;
    suppliers: Array<{ id: number; name: string }>;
    articles: Article[];
}

const props = defineProps<Props>();

const isSubmitting = ref(false);

// Form Data - Preenchido com dados da proposta
const formData = reactive({
    proposal_date: props.proposal.proposal_date,
    client_id: props.proposal.client_id,
    validity_date: props.proposal.validity_date,
    status: props.proposal.status,
    items: props.proposal.items.map((item) => ({
        article_id: String(item.article_id),
        supplier_id: item.supplier_id ? String(item.supplier_id) : '',
        quantity: item.quantity,
        unit_price: item.unit_price,
        cost_price: item.cost_price,
        notes: item.notes || '',
    })),
});

// Computed
const totalAmount = computed(() => {
    return formData.items.reduce((sum, item) => {
        return sum + (item.quantity || 0) * (item.unit_price || 0);
    }, 0);
});

// Methods
const goBack = () => {
    router.get('/proposals');
};

const addItem = () => {
    formData.items.push({
        article_id: '',
        supplier_id: '',
        quantity: 1,
        unit_price: 0,
        cost_price: null,
        notes: '',
    });
};

const removeItem = (index: number) => {
    formData.items.splice(index, 1);
};

const onArticleChange = (index: number) => {
    const item = formData.items[index];
    const article = props.articles.find((a) => a.id === Number(item.article_id));
    if (article) {
        item.unit_price = article.price;
    }
};

const calculateItemTotal = (index: number) => {
    // Trigger reactivity
    formData.items[index] = { ...formData.items[index] };
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};

const submitForm = () => {
    if (formData.items.length === 0) {
        alert('Adicione pelo menos um artigo à proposta.');
        return;
    }

    isSubmitting.value = true;

    router.put(`/proposals/${props.proposal.id}`, formData, {
        preserveScroll: true,
        onSuccess: () => {
            isSubmitting.value = false;
        },
        onError: (errors) => {
            isSubmitting.value = false;
            console.error('Erros no formulário:', errors);
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

