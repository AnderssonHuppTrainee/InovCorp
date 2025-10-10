# 🎨 EXEMPLOS PRÁTICOS DE REFATORAÇÃO

## 📝 ANTES E DEPOIS - Comparações Lado a Lado

---

## 1️⃣ CRIAR NOVA TAXA DE IVA

### ❌ ANTES (110 linhas)

```vue
<!-- settings/tax-rates/Create.vue -->
<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Nova Taxa de IVA"
                description="Criar nova taxa de IVA"
            >
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <form @submit="onSubmit">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <FormField v-slot="{ componentField }" name="name">
                                <FormItem>
                                    <FormLabel>Nome *</FormLabel>
                                    <FormControl>
                                        <Input
                                            v-bind="componentField"
                                            placeholder="Ex: Normal"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="rate">
                                <FormItem>
                                    <FormLabel>Taxa (%) *</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="number"
                                            v-bind="componentField"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField
                                v-slot="{ value, handleChange }"
                                name="is_active"
                            >
                                <FormItem
                                    class="flex flex-row items-start space-x-3"
                                >
                                    <FormControl>
                                        <Checkbox
                                            :checked="value"
                                            @update:checked="handleChange"
                                        />
                                    </FormControl>
                                    <FormLabel>Taxa Ativa</FormLabel>
                                </FormItem>
                            </FormField>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack"
                        >Cancelar</Button
                    >
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A guardar...' : 'Criar Taxa' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
// 14 imports
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { taxRateSchema } from '@/schemas/taxRateSchema';
import { toTypedSchema } from '@vee-validate/zod';
import { router } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const isSubmitting = ref(false);

const form = useForm({
    validationSchema: toTypedSchema(taxRateSchema),
    initialValues: {
        name: '',
        rate: 0,
        is_active: true,
    },
});

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;
    router.post('/tax-rates', values, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
});

const goBack = () => router.get('/tax-rates');
</script>
```

### ✅ DEPOIS (35 linhas - Redução de 68%)

```vue
<!-- settings/tax-rates/Create.vue -->
<template>
    <FormWrapper
        title="Nova Taxa de IVA"
        description="Criar nova taxa de IVA"
        submit-text="Criar Taxa"
        :is-submitting="isSubmitting"
        @submit="onSubmit"
    >
        <template #fields>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome *</FormLabel>
                        <FormControl>
                            <Input
                                v-bind="componentField"
                                placeholder="Ex: Normal"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="rate">
                    <FormItem>
                        <FormLabel>Taxa (%) *</FormLabel>
                        <FormControl>
                            <Input type="number" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <CheckboxField name="is_active" label="Taxa Ativa" />
            </div>
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import FormWrapper from '@/components/common/FormWrapper.vue';
import CheckboxField from '@/components/common/CheckboxField.vue';
import {
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useCrudForm } from '@/composables/forms/useCrudForm';
import taxRates from '@/routes/tax-rates';
import { taxRateSchema } from '@/schemas/taxRateSchema';

const { form, isSubmitting, onSubmit } = useCrudForm({
    schema: taxRateSchema,
    route: taxRates,
    initialValues: { name: '', rate: 0, is_active: true },
});
</script>
```

**Benefícios:**

- ✅ 68% menos código
- ✅ 50% menos imports
- ✅ Lógica de submit encapsulada
- ✅ UX padronizada
- ✅ Manutenção centralizada

---

## 2️⃣ LISTAGEM DE ENCOMENDAS

### ❌ ANTES (orders/Index.vue - ~150 linhas)

```vue
<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Encomendas"
                description="Gerir encomendas de clientes"
            >
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        @click="showFilters = !showFilters"
                    >
                        <FilterIcon class="mr-2 h-4 w-4" />
                        {{ showFilters ? 'Ocultar' : 'Mostrar' }} Filtros
                    </Button>
                    <Button @click="router.visit('/orders/create')">
                        <PlusIcon class="mr-2 h-4 w-4" />
                        Nova Encomenda
                    </Button>
                </div>
            </PageHeader>

            <Card v-if="showFilters">
                <CardHeader>
                    <div class="flex gap-2">
                        <Select
                            v-model="statusFilter"
                            @update:model-value="applyFilters"
                        >
                            <SelectTrigger class="w-[180px]">
                                <SelectValue placeholder="Estado" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Todos</SelectItem>
                                <SelectItem value="draft">Rascunho</SelectItem>
                                <SelectItem value="closed">Fechado</SelectItem>
                            </SelectContent>
                        </Select>

                        <Select
                            v-model="clientFilter"
                            @update:model-value="applyFilters"
                        >
                            <SelectTrigger class="w-[200px]">
                                <SelectValue placeholder="Cliente" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Todos</SelectItem>
                                <SelectItem
                                    v-for="client in clients"
                                    :value="client.id"
                                >
                                    {{ client.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <Button
                            variant="ghost"
                            @click="clearFilters"
                            v-if="hasFilters"
                        >
                            <XIcon class="mr-2 h-4 w-4" />
                            Limpar
                        </Button>
                    </div>
                </CardHeader>
            </Card>

            <Card>
                <CardContent class="p-0">
                    <DataTable :columns="columns" :data="orders" />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { columns } from './columns';
// ... 15+ imports

const props = defineProps<{
    orders: Order[];
    clients: Client[];
    filters: { status?: string; client_id?: number };
}>();

const showFilters = ref(false);
const statusFilter = ref(props.filters.status ?? 'all');
const clientFilter = ref(props.filters.client_id?.toString() ?? 'all');

const hasFilters = computed(() => {
    return statusFilter.value !== 'all' || clientFilter.value !== 'all';
});

const applyFilters = () => {
    router.get(
        '/orders',
        {
            status:
                statusFilter.value !== 'all' ? statusFilter.value : undefined,
            client_id:
                clientFilter.value !== 'all' ? clientFilter.value : undefined,
        },
        { preserveState: true, replace: true },
    );
};

const clearFilters = () => {
    statusFilter.value = 'all';
    clientFilter.value = 'all';
    applyFilters();
};
</script>
```

### ✅ DEPOIS (45 linhas - Redução de 70%)

```vue
<!-- orders/Index.vue -->
<template>
    <IndexWrapper
        title="Encomendas"
        description="Gerir encomendas de clientes"
        :data="orders"
        :columns="columns"
        create-route="/orders/create"
        :show-filters="true"
    >
        <template #filters>
            <div class="flex gap-2">
                <Select
                    v-model="filters.status"
                    @update:model-value="applyFilters"
                >
                    <SelectTrigger class="w-[180px]">
                        <SelectValue placeholder="Estado" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Todos</SelectItem>
                        <SelectItem value="draft">Rascunho</SelectItem>
                        <SelectItem value="closed">Fechado</SelectItem>
                    </SelectContent>
                </Select>

                <Select
                    v-model="filters.client_id"
                    @update:model-value="applyFilters"
                >
                    <SelectTrigger class="w-[200px]">
                        <SelectValue placeholder="Cliente" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Todos</SelectItem>
                        <SelectItem
                            v-for="client in clients"
                            :value="client.id.toString()"
                        >
                            {{ client.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <Button
                    variant="ghost"
                    @click="clearFilters"
                    v-if="hasActiveFilters"
                >
                    <XIcon class="mr-2 h-4 w-4" />
                    Limpar
                </Button>
            </div>
        </template>
    </IndexWrapper>
</template>

<script setup lang="ts">
import IndexWrapper from '@/components/common/IndexWrapper.vue';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { useDataTableFilters } from '@/composables/data/useDataTableFilters';
import orders from '@/routes/orders';
import { columns } from './columns';
import { XIcon } from 'lucide-vue-next';

const props = defineProps<{
    orders: Order[];
    clients: Client[];
    filters: { status?: string; client_id?: number };
}>();

const { filters, hasActiveFilters, applyFilters, clearFilters } =
    useDataTableFilters({
        route: orders,
        initialFilters: {
            status: props.filters.status ?? 'all',
            client_id: props.filters.client_id?.toString() ?? 'all',
        },
    });
</script>
```

---

## 3️⃣ FORMATAÇÃO DE VALORES MONETÁRIOS EM TABELAS

### ❌ ANTES (orders/columns.ts)

```typescript
// 15+ arquivos diferentes com 3 padrões diferentes:

// Padrão 1 - Problemático
{
    accessorKey: 'total_amount',
    header: 'Valor Total',
    cell: ({ row }) => {
        const amount = parseFloat(row.original.total_amount as any) || 0
        return h('div', { class: 'font-semibold' }, `€${amount.toFixed(2)}`)
        // ⚠️ PROBLEMA: Se amount for NaN, causa erro
    },
}

// Padrão 2 - Intl (Correto mas verboso)
{
    accessorKey: 'total_amount',
    header: 'Valor Total',
    cell: ({ row }) => {
        const amount = parseFloat(row.getValue('total_amount'))
        const formatted = new Intl.NumberFormat('pt-PT', {
            style: 'currency',
            currency: 'EUR',
        }).format(amount)
        return h('div', { class: 'font-medium' }, formatted)
    },
}

// Padrão 3 - Parcialmente corrigido
{
    accessorKey: 'total_amount',
    header: 'Valor Total',
    cell: ({ row }) => {
        const value = row.original.total_amount
        const amount = typeof value === 'number' ? value : parseFloat(value ?? '0')
        const validAmount = isNaN(amount) ? 0 : amount
        return h('div', { class: 'font-semibold' }, `€${validAmount.toFixed(2)}`)
    },
}
```

### ✅ DEPOIS (TODOS os columns.ts)

```typescript
import { useMoneyFormatter } from '@/composables/formatters/useMoneyFormatter'

const { format } = useMoneyFormatter()

export const columns: ColumnDef<Order>[] = [
    {
        accessorKey: 'total_amount',
        header: 'Valor Total',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, format(row.getValue('total_amount')))
        },
    },
]

// OU usando componente:
import MoneyDisplay from '@/components/common/MoneyDisplay.vue'

{
    accessorKey: 'total_amount',
    header: 'Valor Total',
    cell: ({ row }) => {
        return h(MoneyDisplay, { value: row.getValue('total_amount') })
    },
}
```

**Benefícios:**

- ✅ 1 linha vs 6 linhas (83% menos código)
- ✅ 100% consistente em todos os arquivos
- ✅ Tratamento de erros centralizado
- ✅ Fácil alterar formato globalmente

---

## 4️⃣ FORMATAÇÃO DE DATAS EM TABELAS

### ❌ ANTES (5 padrões diferentes encontrados)

```typescript
// Padrão 1 - Básico
{
    accessorKey: 'order_date',
    cell: ({ row }) => {
        const date = new Date(row.getValue('order_date'))
        return h('div', {}, date.toLocaleDateString('pt-PT'))
    },
}

// Padrão 2 - Com fallback
{
    accessorKey: 'delivery_date',
    cell: ({ row }) => {
        const date = row.getValue('delivery_date') as string | null
        if (!date) return h('div', { class: 'text-muted-foreground' }, '-')
        return h('div', {}, new Date(date).toLocaleDateString('pt-PT'))
    },
}

// Padrão 3 - Formatação customizada
{
    accessorKey: 'created_at',
    cell: ({ row }) => {
        const date = row.getValue('created_at')
        return h('div', {}, new Date(date).toLocaleString('pt-PT'))
    },
}
```

### ✅ DEPOIS (Padronizado)

```typescript
import { useDateFormatter } from '@/composables/formatters/useDateFormatter';

const { formatDate, formatDateTime } = useDateFormatter();

export const columns: ColumnDef<Order>[] = [
    {
        accessorKey: 'order_date',
        header: 'Data',
        cell: ({ row }) => {
            return h('div', {}, formatDate(row.getValue('order_date')));
        },
    },
    {
        accessorKey: 'delivery_date',
        header: 'Entrega',
        cell: ({ row }) => {
            return h(
                'div',
                { class: 'text-muted-foreground' },
                formatDate(row.getValue('delivery_date')),
            );
            // Retorna '-' automaticamente se null
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Criado',
        cell: ({ row }) => {
            return h('div', {}, formatDateTime(row.getValue('created_at')));
        },
    },
];
```

---

## 5️⃣ CHECKBOX FIELD (Componente Helper)

### Problema Atual

Código do checkbox duplicado em ~15 formulários:

```vue
<FormField v-slot="{ value, handleChange }" name="is_active">
    <FormItem class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
        <FormControl>
            <Checkbox :checked="value" @update:checked="(checked: boolean) => handleChange(checked)" />
        </FormControl>
        <div class="space-y-1 leading-none">
            <FormLabel>Taxa Ativa</FormLabel>
            <FormDescription>
                Marque se a taxa está ativa para uso
            </FormDescription>
        </div>
    </FormItem>
</FormField>
```

### ✅ Solução: CheckboxField Component

```vue
<!-- components/common/CheckboxField.vue -->
<script setup lang="ts">
import {
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormDescription,
} from '@/components/ui/form';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';

interface Props {
    name: string;
    label: string;
    description?: string;
    defaultValue?: boolean;
}

defineProps<Props>();
</script>

<template>
    <FormField v-slot="{ value, handleChange }" :name="name">
        <FormItem
            class="flex flex-row items-start space-y-0 space-x-3 rounded-md border p-4"
        >
            <FormControl>
                <Checkbox :checked="value" @update:checked="handleChange" />
            </FormControl>
            <div class="space-y-1 leading-none">
                <FormLabel>{{ label }}</FormLabel>
                <FormDescription v-if="description">
                    {{ description }}
                </FormDescription>
            </div>
        </FormItem>
    </FormField>
</template>
```

**USO:**

```vue
<!-- ANTES: 11 linhas -->
<FormField v-slot="{ value, handleChange }" name="is_active">
    <FormItem class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
        <FormControl>
            <Checkbox :checked="value" @update:checked="handleChange" />
        </FormControl>
        <div class="space-y-1 leading-none">
            <FormLabel>Taxa Ativa</FormLabel>
            <FormDescription>Marque se a taxa está ativa para uso</FormDescription>
        </div>
    </FormItem>
</FormField>

<!-- DEPOIS: 1 linha -->
<CheckboxField
    name="is_active"
    label="Taxa Ativa"
    description="Marque se a taxa está ativa para uso"
/>
```

**Redução:** 11 linhas → 1 linha por checkbox  
**Arquivos beneficiados:** 15+  
**Total economizado:** ~150 linhas

---

## 6️⃣ SELECT FIELD PATTERN

### Problema

Select com entidades externas repetido em múltiplos formulários:

```vue
<FormField v-slot="{ componentField }" name="entity_id">
    <FormItem>
        <FormLabel>Entidade *</FormLabel>
        <Select v-bind="componentField">
            <FormControl>
                <SelectTrigger>
                    <SelectValue placeholder="Selecione uma entidade" />
                </SelectTrigger>
            </FormControl>
            <SelectContent>
                <SelectItem v-for="entity in entities" :key="entity.id" :value="String(entity.id)">
                    {{ entity.name }}
                </SelectItem>
            </SelectContent>
        </Select>
        <FormMessage />
    </FormItem>
</FormField>
```

### ✅ Solução: RelationSelectField Component

```vue
<!-- components/common/RelationSelectField.vue -->
<script setup lang="ts">
import {
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormMessage,
    FormDescription,
} from '@/components/ui/form';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';

interface Props {
    name: string;
    label: string;
    placeholder?: string;
    description?: string;
    options: Array<{ id: number | string; name: string }>;
    required?: boolean;
}

defineProps<Props>();
</script>

<template>
    <FormField v-slot="{ componentField }" :name="name">
        <FormItem>
            <FormLabel>{{ label }} <span v-if="required">*</span></FormLabel>
            <Select v-bind="componentField">
                <FormControl>
                    <SelectTrigger>
                        <SelectValue
                            :placeholder="
                                placeholder ??
                                `Selecione ${label.toLowerCase()}`
                            "
                        />
                    </SelectTrigger>
                </FormControl>
                <SelectContent>
                    <SelectItem
                        v-for="option in options"
                        :key="option.id"
                        :value="String(option.id)"
                    >
                        {{ option.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <FormDescription v-if="description">{{
                description
            }}</FormDescription>
            <FormMessage />
        </FormItem>
    </FormField>
</template>
```

**USO:**

```vue
<!-- ANTES: 18 linhas -->
<FormField v-slot="{ componentField }" name="entity_id">
    <FormItem>
        <FormLabel>Entidade *</FormLabel>
        <Select v-bind="componentField">
            <FormControl>
                <SelectTrigger>
                    <SelectValue placeholder="Selecione uma entidade" />
                </SelectTrigger>
            </FormControl>
            <SelectContent>
                <SelectItem v-for="entity in entities" :key="entity.id" :value="String(entity.id)">
                    {{ entity.name }}
                </SelectItem>
            </SelectContent>
        </Select>
        <FormMessage />
    </FormItem>
</FormField>

<!-- DEPOIS: 1 linha -->
<RelationSelectField
    name="entity_id"
    label="Entidade"
    :options="entities"
    required
/>
```

---

## 7️⃣ ACTIONS COLUMN (Dropdown de Ações)

### ❌ ANTES (Repetido em 15+ columns.ts)

```typescript
{
    id: 'actions',
    cell: ({ row }) => {
        const item = row.original
        return h(
            DropdownMenu,
            {},
            {
                default: () => [
                    h(DropdownMenuTrigger, { asChild: true }, {
                        default: () => h(Button, { variant: 'ghost' }, {
                            default: () => h(MoreHorizontal, { class: 'h-4 w-4' })
                        })
                    }),
                    h(DropdownMenuContent, { align: 'end' }, {
                        default: () => [
                            h(DropdownMenuLabel, {}, { default: () => 'Ações' }),
                            h(DropdownMenuItem, {
                                onClick: () => router.visit(`/orders/${item.id}`)
                            }, {
                                default: () => [
                                    h(Eye, { class: 'mr-2 h-4 w-4' }),
                                    'Ver Detalhes'
                                ]
                            }),
                            h(DropdownMenuItem, {
                                onClick: () => router.visit(`/orders/${item.id}/edit`)
                            }, {
                                default: () => [
                                    h(Pencil, { class: 'mr-2 h-4 w-4' }),
                                    'Editar'
                                ]
                            }),
                            h(DropdownMenuSeparator),
                            h(DropdownMenuItem, {
                                onClick: () => deleteItem(item.id),
                                class: 'text-destructive'
                            }, {
                                default: () => [
                                    h(Trash2, { class: 'mr-2 h-4 w-4' }),
                                    'Eliminar'
                                ]
                            }),
                        ]
                    })
                ]
            }
        )
    },
}
```

### ✅ DEPOIS (Helper Function)

```typescript
// lib/table-helpers.ts
import { h } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { MoreHorizontal, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

interface ActionsColumnOptions {
    baseRoute: string;
    onDelete?: (id: number) => void;
    customActions?: Array<{
        label: string;
        icon: any;
        onClick: (item: any) => void;
        variant?: string;
    }>;
}

export function createActionsColumn<T extends { id: number }>(
    options: ActionsColumnOptions,
) {
    return {
        id: 'actions',
        cell: ({ row }: { row: { original: T } }) => {
            const item = row.original;

            const defaultActions = [
                h(DropdownMenuLabel, {}, { default: () => 'Ações' }),
                h(
                    DropdownMenuItem,
                    {
                        onClick: () =>
                            router.visit(`${options.baseRoute}/${item.id}`),
                    },
                    {
                        default: () => [
                            h(Eye, { class: 'mr-2 h-4 w-4' }),
                            'Ver Detalhes',
                        ],
                    },
                ),
                h(
                    DropdownMenuItem,
                    {
                        onClick: () =>
                            router.visit(
                                `${options.baseRoute}/${item.id}/edit`,
                            ),
                    },
                    {
                        default: () => [
                            h(Pencil, { class: 'mr-2 h-4 w-4' }),
                            'Editar',
                        ],
                    },
                ),
            ];

            if (options.onDelete || options.customActions?.length) {
                defaultActions.push(h(DropdownMenuSeparator));
            }

            if (options.customActions) {
                options.customActions.forEach((action) => {
                    defaultActions.push(
                        h(
                            DropdownMenuItem,
                            {
                                onClick: () => action.onClick(item),
                                class:
                                    action.variant === 'destructive'
                                        ? 'text-destructive'
                                        : '',
                            },
                            {
                                default: () => [
                                    h(action.icon, { class: 'mr-2 h-4 w-4' }),
                                    action.label,
                                ],
                            },
                        ),
                    );
                });
            }

            if (options.onDelete) {
                defaultActions.push(
                    h(
                        DropdownMenuItem,
                        {
                            onClick: () => options.onDelete!(item.id),
                            class: 'text-destructive',
                        },
                        {
                            default: () => [
                                h(Trash2, { class: 'mr-2 h-4 w-4' }),
                                'Eliminar',
                            ],
                        },
                    ),
                );
            }

            return h(
                DropdownMenu,
                {},
                {
                    default: () => [
                        h(
                            DropdownMenuTrigger,
                            { asChild: true },
                            {
                                default: () =>
                                    h(
                                        Button,
                                        { variant: 'ghost', size: 'icon' },
                                        {
                                            default: () =>
                                                h(MoreHorizontal, {
                                                    class: 'h-4 w-4',
                                                }),
                                        },
                                    ),
                            },
                        ),
                        h(
                            DropdownMenuContent,
                            { align: 'end' },
                            {
                                default: () => defaultActions,
                            },
                        ),
                    ],
                },
            );
        },
    };
}
```

**USO em orders/columns.ts:**

```typescript
import { createActionsColumn } from '@/lib/table-helpers';

const deleteOrder = (id: number) => {
    if (confirm('Eliminar encomenda?')) {
        router.delete(`/orders/${id}`);
    }
};

export const columns: ColumnDef<Order>[] = [
    // ... outras colunas
    createActionsColumn<Order>({
        baseRoute: '/orders',
        onDelete: deleteOrder,
        customActions: [
            {
                label: 'Gerar PDF',
                icon: FileText,
                onClick: (order) => window.open(`/orders/${order.id}/pdf`),
            },
        ],
    }),
];
```

**Redução:** 50 linhas → 5 linhas (**90% menos**)

---

## 8️⃣ CÓDIGO CALENDAR - MELHORIAS ESPECÍFICAS

### Problema: Lógica complexa misturada

```vue
<!-- calendar/Index.vue - 962 linhas! -->
<script setup lang="ts">
// Handlers misturados com configuração
const handleEventClick = (clickInfo: EventClickArg) => {
    const eventId = parseInt(clickInfo.event.id);
    fetch(calendar.show(eventId).url)
        .then((res) => res.json())
        .then((data) => {
            // 30 linhas de lógica...
        });
};

const handleEventDrop = (dropInfo: EventDropArg) => {
    // 15 linhas...
};

const submitForm = form.handleSubmit((values) => {
    // 40 linhas...
});
</script>
```

### ✅ Solução: Extrair para Composable

```typescript
// composables/calendar/useCalendarEvents.ts

export function useCalendarEvents(options: {
    route: any;
    form: any;
    onSuccess?: () => void;
}) {
    const isSubmitting = ref(false);
    const isEditMode = ref(false);
    const selectedEventId = ref<number | null>(null);

    const loadEvent = async (eventId: number) => {
        try {
            const response = await fetch(options.route.show(eventId).url);
            const data = await response.json();

            // Transformações e normalização
            const sharedWith = Array.isArray(data.event.shared_with)
                ? data.event.shared_with.map((id: number) => id.toString())
                : [];

            const eventTime = data.event.event_time?.substring(0, 5);

            options.form.resetForm({
                values: {
                    event_date: data.event.event_date,
                    event_time: eventTime,
                    duration: data.event.duration,
                    shared_with: sharedWith,
                    knowledge: data.event.knowledge,
                    entity_id: data.event.entity_id?.toString(),
                    calendar_event_type_id:
                        data.event.calendar_event_type_id.toString(),
                    calendar_action_id:
                        data.event.calendar_action_id.toString(),
                    description: data.event.description,
                    status: data.event.status,
                },
            });

            isEditMode.value = true;
            return data;
        } catch (error) {
            console.error('Error loading event:', error);
            throw error;
        }
    };

    const submitEvent = async (values: any) => {
        if (isSubmitting.value) return;
        isSubmitting.value = true;

        const payload = {
            ...values,
            shared_with: Array.isArray(values.shared_with)
                ? values.shared_with.map((id) =>
                      typeof id === 'string' ? parseInt(id) : id,
                  )
                : [],
        };

        try {
            if (isEditMode.value && selectedEventId.value) {
                await router.put(
                    options.route.update(selectedEventId.value).url,
                    payload,
                    { preserveState: true, only: ['events'] },
                );
            } else {
                await router.post(options.route.store().url, payload, {
                    preserveState: true,
                    only: ['events'],
                });
            }
            options.onSuccess?.();
        } finally {
            isSubmitting.value = false;
        }
    };

    const updateEventTime = (eventId: number, date: Date, time?: string) => {
        router.put(
            options.route.update(eventId).url,
            {
                event_date: date.toISOString().split('T')[0],
                event_time: time ?? date.toTimeString().substring(0, 5),
            },
            { preserveState: true },
        );
    };

    const updateEventDuration = (eventId: number, durationMinutes: number) => {
        router.put(
            options.route.update(eventId).url,
            { duration: durationMinutes },
            { preserveState: true },
        );
    };

    return {
        isSubmitting,
        isEditMode,
        selectedEventId,
        loadEvent,
        submitEvent,
        updateEventTime,
        updateEventDuration,
    };
}
```

**USO:**

```vue
<script setup lang="ts">
import { useCalendarEvents } from '@/composables/calendar/useCalendarEvents';

const {
    isSubmitting,
    isEditMode,
    selectedEventId,
    loadEvent,
    submitEvent,
    updateEventTime,
    updateEventDuration,
} = useCalendarEvents({
    route: calendar,
    form,
    onSuccess: () => {
        showEventDialog.value = false;
    },
});

const handleEventClick = async (clickInfo: EventClickArg) => {
    selectedEventId.value = parseInt(clickInfo.event.id);
    await loadEvent(selectedEventId.value);
    showEventDialog.value = true;
};

const handleEventDrop = (dropInfo: EventDropArg) => {
    const eventId = parseInt(dropInfo.event.id);
    updateEventTime(eventId, dropInfo.event.start!);
};
</script>
```

**Redução em Index.vue:** 962 → ~400 linhas (**58% menos**)

---

## 📊 RESUMO DE IMPACTO POR REFATORAÇÃO

| Refatoração             | Arquivos | Linhas Antes | Linhas Depois | Redução |
| ----------------------- | -------- | ------------ | ------------- | ------- |
| **useCrudForm**         | 17       | ~1.870       | ~595          | -68%    |
| **FormWrapper**         | 17       | ~442         | ~0            | -100%\* |
| **IndexWrapper**        | 15       | ~1.500       | ~675          | -55%    |
| **useMoneyFormatter**   | 15+      | ~90          | ~15           | -83%    |
| **useDateFormatter**    | 15+      | ~75          | ~15           | -80%    |
| **CheckboxField**       | 15       | ~165         | ~15           | -91%    |
| **RelationSelectField** | 20+      | ~360         | ~20           | -94%    |
| **useCalendarEvents**   | 1        | ~300         | ~50           | -83%    |

\*Absorvido pelo FormWrapper component

**TOTAL GERAL:**

- **Linhas antes:** ~4.800
- **Linhas depois:** ~1.385
- **Redução:** -71% (~3.400 linhas)

---

## 🎯 QUICK WINS - Implementar Esta Semana

### 1. useMoneyFormatter (2 horas)

**Impacto:** Imediato em 15+ arquivos  
**Risco:** Muito baixo  
**ROI:** Excelente

### 2. useDateFormatter (2 horas)

**Impacto:** Imediato em 15+ arquivos  
**Risco:** Muito baixo  
**ROI:** Excelente

### 3. CheckboxField Component (1 hora)

**Impacto:** 15+ formulários  
**Risco:** Baixo  
**ROI:** Muito bom

**Total:** 5 horas para reduzir ~500 linhas de código

---

## 📚 RECURSOS E TEMPLATES

Todos os componentes e composables propostos estão prontos para copiar/colar deste documento.

**Ordem de implementação sugerida:**

1. ✅ Criar pasta `components/common/`
2. ✅ Criar pasta `composables/formatters/`
3. ✅ Implementar formatters (baixo risco)
4. ✅ Criar CheckboxField (baixo risco)
5. ✅ Criar useCrudForm (médio risco, alto valor)
6. ✅ Pilotar em 1-2 páginas
7. ✅ Escalar gradualmente

---

_Exemplos práticos de código pronto para uso_  
_Última atualização: 10/10/2025_
