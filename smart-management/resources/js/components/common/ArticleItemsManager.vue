<template>
    <Card class="mb-6">
        <CardHeader>
            <div class="flex items-center justify-between">
                <CardTitle>{{ title }}</CardTitle>
                <Button type="button" @click="handleAddItem">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Adicionar Artigo
                </Button>
            </div>
        </CardHeader>
        <CardContent>
            <div
                v-if="items.length === 0"
                class="py-8 text-center text-muted-foreground"
            >
                {{ emptyMessage }}
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="(item, index) in items"
                    :key="index"
                    class="space-y-4 rounded-lg border p-4"
                >
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium">Item {{ index + 1 }}</h4>
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            @click="handleRemoveItem(index)"
                        >
                            <Trash2Icon class="h-4 w-4" />
                        </Button>
                    </div>

                    <div :class="gridColsClass">
                        <!-- Artigo -->
                        <div :class="articleColClass">
                            <label class="text-sm">Artigo *</label>
                            <select
                                :value="item.article_id"
                                @change="handleArticleChange(index, $event)"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                required
                            >
                                <option value="">Selecione</option>
                                <option
                                    v-for="article in articles"
                                    :key="article.id"
                                    :value="article.id"
                                >
                                    {{ article.reference }} - {{ article.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Fornecedor -->
                        <div v-if="showSupplier" class="space-y-2">
                            <label class="text-sm">Fornecedor</label>
                            <select
                                :value="item.supplier_id || ''"
                                @change="handleSupplierChange(index, $event)"
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
                                :value="item.quantity"
                                @input="handleQuantityChange(index, $event)"
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
                                :value="item.unit_price"
                                @input="handleUnitPriceChange(index, $event)"
                                required
                            />
                        </div>

                        <!-- Preço Custo (apenas para proposals) -->
                        <div v-if="showCostPrice" class="space-y-2">
                            <label class="text-sm">Preço Custo</label>
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                :value="item.cost_price ?? ''"
                                @input="handleCostPriceChange(index, $event)"
                            />
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="space-y-2">
                        <label class="text-sm">Notas</label>
                        <Textarea
                            :value="item.notes || ''"
                            @input="handleNotesChange(index, $event)"
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
                                (item.quantity || 0) * (item.unit_price || 0),
                            )
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Geral -->
            <div v-if="items.length > 0" class="mt-6 border-t pt-4 text-right">
                <div class="text-lg">
                    <span class="text-muted-foreground">Total:</span>
                    <span class="ml-4 text-2xl font-bold">{{
                        formatCurrency(totalAmount)
                    }}</span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Article } from '@/types';
import { PlusIcon, Trash2Icon } from 'lucide-vue-next';
import { computed } from 'vue';

interface Supplier {
    id: number;
    name: string;
}

interface Item {
    article_id: string | number;
    supplier_id?: string | number;
    quantity: number;
    unit_price: number;
    cost_price?: number | null;
    notes?: string | null;
}

interface Props {
    modelValue: Item[];
    articles: Article[];
    suppliers?: Supplier[];
    title?: string;
    emptyMessage?: string;
    showSupplier?: boolean;
    showCostPrice?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    suppliers: () => [],
    title: 'Artigos',
    emptyMessage:
        'Nenhum artigo adicionado. Clique em "Adicionar Artigo" para começar.',
    showSupplier: true,
    showCostPrice: false,
});

const emit = defineEmits<{
    'update:modelValue': [items: Item[]];
}>();

const items = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const gridColsClass = computed(() => {
    if (props.showCostPrice) {
        return 'grid grid-cols-1 gap-4 md:grid-cols-6';
    }
    return 'grid grid-cols-1 gap-4 md:grid-cols-5';
});

const articleColClass = computed(() => {
    return props.showCostPrice
        ? 'space-y-2 md:col-span-2'
        : 'space-y-2 md:col-span-2';
});

const totalAmount = computed(() => {
    return items.value.reduce((sum, item) => {
        return sum + (item.quantity || 0) * (item.unit_price || 0);
    }, 0);
});

const handleAddItem = () => {
    const newItem: Item = {
        article_id: '',
        supplier_id: '',
        quantity: 1,
        unit_price: 0,
        cost_price: null,
        notes: '',
    };
    if (props.showCostPrice) {
        newItem.cost_price = null;
    }
    items.value = [...items.value, newItem];
};

const handleRemoveItem = (index: number) => {
    items.value = items.value.filter((_, i) => i !== index);
};

const handleArticleChange = (index: number, event: Event) => {
    const target = event.target as HTMLSelectElement;
    const articleId = target.value;
    const newItems = [...items.value];
    newItems[index] = { ...newItems[index], article_id: articleId };

    // Auto-fill price if article selected
    if (articleId) {
        const article = props.articles.find((a) => a.id === Number(articleId));
        if (article) {
            newItems[index].unit_price = article.price;
        }
    }

    items.value = newItems;
};

const handleSupplierChange = (index: number, event: Event) => {
    const target = event.target as HTMLSelectElement;
    const newItems = [...items.value];
    newItems[index] = { ...newItems[index], supplier_id: target.value };
    items.value = newItems;
};

const handleQuantityChange = (index: number, event: Event) => {
    const target = event.target as HTMLInputElement;
    const newItems = [...items.value];
    newItems[index] = {
        ...newItems[index],
        quantity: parseFloat(target.value) || 0,
    };
    items.value = newItems;
};

const handleUnitPriceChange = (index: number, event: Event) => {
    const target = event.target as HTMLInputElement;
    const newItems = [...items.value];
    newItems[index] = {
        ...newItems[index],
        unit_price: parseFloat(target.value) || 0,
    };
    items.value = newItems;
};

const handleCostPriceChange = (index: number, event: Event) => {
    const target = event.target as HTMLInputElement;
    const newItems = [...items.value];
    newItems[index] = {
        ...newItems[index],
        cost_price: target.value ? parseFloat(target.value) : null,
    };
    items.value = newItems;
};

const handleNotesChange = (index: number, event: Event) => {
    const target = event.target as HTMLTextAreaElement;
    const newItems = [...items.value];
    newItems[index] = { ...newItems[index], notes: target.value };
    items.value = newItems;
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>
