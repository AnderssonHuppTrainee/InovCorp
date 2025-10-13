<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useDateFormatter } from '@/composables/formatters/useDateFormatter';
import { useMoneyFormatter } from '@/composables/formatters/useMoneyFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import {
    Activity,
    AlertCircle,
    Briefcase,
    CheckCircle2,
    Clock,
    DollarSign,
    FileText,
    ShoppingCart,
    TrendingDown,
    TrendingUp,
    Truck,
    Users,
} from 'lucide-vue-next';

interface Stats {
    entities: {
        total_clients: number;
        total_suppliers: number;
        active_entities: number;
    };
    sales: {
        total_proposals: number;
        draft_proposals: number;
        total_orders: number;
        draft_orders: number;
    };
    work_orders: {
        total: number;
        pending: number;
        in_progress: number;
    };
    financials: {
        customer_invoices: {
            total: number;
            pending: number;
            overdue: number;
            paid: number;
        };
        supplier_invoices: {
            total: number;
            pending: number;
            overdue: number;
            paid: number;
        };
        revenue: {
            total: number;
            pending: number;
        };
        expenses: {
            total: number;
            pending: number;
        };
    };
}

interface RecentActivity {
    proposals: any[];
    orders: any[];
    work_orders: any[];
}

interface Props {
    stats: Stats;
    recent_activities: RecentActivity;
    charts: {
        sales_by_month: Array<{ month: string; total: number }>;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const { format } = useMoneyFormatter();
const { formatDate } = useDateFormatter();

const profit =
    (props.stats.financials.revenue.total || 0) -
    (props.stats.financials.expenses.total || 0);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                <p class="text-muted-foreground">Visão geral do seu negócio</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Clientes</CardTitle
                        >
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.entities.total_clients }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.entities.active_entities }} ativos
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Fornecedores</CardTitle
                        >
                        <Truck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.entities.total_suppliers }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Base de fornecedores
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Propostas</CardTitle
                        >
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.sales.total_proposals }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.sales.draft_proposals }} em rascunho
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Encomendas</CardTitle
                        >
                        <ShoppingCart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.sales.total_orders }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.sales.draft_orders }} em rascunho
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Receita Total</CardTitle
                        >
                        <TrendingUp class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ format(stats.financials.revenue.total) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ format(stats.financials.revenue.pending) }}
                            pendente
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Despesas Totais</CardTitle
                        >
                        <TrendingDown class="h-4 w-4 text-red-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">
                            {{ format(stats.financials.expenses.total) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ format(stats.financials.expenses.pending) }}
                            pendente
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">Lucro</CardTitle>
                        <DollarSign
                            :class="
                                profit >= 0 ? 'text-green-600' : 'text-red-600'
                            "
                            class="h-4 w-4"
                        />
                    </CardHeader>
                    <CardContent>
                        <div
                            class="text-2xl font-bold"
                            :class="
                                profit >= 0 ? 'text-green-600' : 'text-red-600'
                            "
                        >
                            {{ format(profit) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Receita - Despesas
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Work Orders</CardTitle
                        >
                        <Briefcase class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.work_orders.in_progress }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Em progresso de {{ stats.work_orders.total }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Activity class="h-5 w-5" />
                            Faturas de Clientes
                        </CardTitle>
                        <CardDescription
                            >Estado das faturas emitidas</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <CheckCircle2
                                        class="h-4 w-4 text-green-600"
                                    />
                                    <span class="text-sm font-medium"
                                        >Pagas</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold">{{
                                        stats.financials.customer_invoices.paid
                                    }}</span>
                                    <Badge
                                        variant="outline"
                                        class="border-green-200 bg-green-50 text-green-700"
                                    >
                                        {{
                                            format(
                                                stats.financials.revenue.total,
                                            )
                                        }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Clock class="h-4 w-4 text-blue-600" />
                                    <span class="text-sm font-medium"
                                        >Pendentes</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold">{{
                                        stats.financials.customer_invoices
                                            .pending
                                    }}</span>
                                    <Badge
                                        variant="outline"
                                        class="border-blue-200 bg-blue-50 text-blue-700"
                                    >
                                        {{
                                            format(
                                                stats.financials.revenue
                                                    .pending,
                                            )
                                        }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <AlertCircle class="h-4 w-4 text-red-600" />
                                    <span class="text-sm font-medium"
                                        >Atrasadas</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold">{{
                                        stats.financials.customer_invoices
                                            .overdue
                                    }}</span>
                                    <Badge variant="destructive">
                                        Atenção!
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Truck class="h-5 w-5" />
                            Faturas de Fornecedores
                        </CardTitle>
                        <CardDescription>Faturas a pagar</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <CheckCircle2
                                        class="h-4 w-4 text-green-600"
                                    />
                                    <span class="text-sm font-medium"
                                        >Pagas</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold">{{
                                        stats.financials.supplier_invoices.paid
                                    }}</span>
                                    <Badge
                                        variant="outline"
                                        class="border-green-200 bg-green-50 text-green-700"
                                    >
                                        {{
                                            format(
                                                stats.financials.expenses.total,
                                            )
                                        }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Clock class="h-4 w-4 text-orange-600" />
                                    <span class="text-sm font-medium"
                                        >A Pagar</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold">{{
                                        stats.financials.supplier_invoices
                                            .pending
                                    }}</span>
                                    <Badge
                                        variant="outline"
                                        class="border-orange-200 bg-orange-50 text-orange-700"
                                    >
                                        {{
                                            format(
                                                stats.financials.expenses
                                                    .pending,
                                            )
                                        }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <AlertCircle class="h-4 w-4 text-red-600" />
                                    <span class="text-sm font-medium"
                                        >Atrasadas</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold">{{
                                        stats.financials.supplier_invoices
                                            .overdue
                                    }}</span>
                                    <Badge variant="destructive">
                                        Urgente!
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base"
                            >Propostas Recentes</CardTitle
                        >
                        <CardDescription
                            >Últimas 5 propostas criadas</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-if="recent_activities.proposals.length === 0"
                                class="py-4 text-center text-sm text-muted-foreground"
                            >
                                Nenhuma proposta ainda
                            </div>
                            <div
                                v-for="proposal in recent_activities.proposals"
                                :key="proposal.id"
                                class="flex items-center justify-between border-b pb-2 last:border-0"
                            >
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium"
                                        >#{{ proposal.number }}</span
                                    >
                                    <span class="text-xs text-muted-foreground">
                                        {{
                                            proposal.client?.name ||
                                            'Cliente não definido'
                                        }}
                                    </span>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-sm font-bold">{{
                                        format(proposal.total_amount)
                                    }}</span>
                                    <Badge
                                        :variant="
                                            proposal.status === 'draft'
                                                ? 'outline'
                                                : 'secondary'
                                        "
                                        class="text-xs"
                                    >
                                        {{ proposal.status }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-base"
                            >Encomendas Recentes</CardTitle
                        >
                        <CardDescription
                            >Últimas 5 encomendas criadas</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-if="recent_activities.orders.length === 0"
                                class="py-4 text-center text-sm text-muted-foreground"
                            >
                                Nenhuma encomenda ainda
                            </div>
                            <div
                                v-for="order in recent_activities.orders"
                                :key="order.id"
                                class="flex items-center justify-between border-b pb-2 last:border-0"
                            >
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium"
                                        >#{{ order.number }}</span
                                    >
                                    <span class="text-xs text-muted-foreground">
                                        {{
                                            order.client?.name ||
                                            'Cliente não definido'
                                        }}
                                    </span>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-sm font-bold">{{
                                        format(order.total_amount)
                                    }}</span>
                                    <Badge
                                        :variant="
                                            order.status === 'draft'
                                                ? 'outline'
                                                : 'default'
                                        "
                                        class="text-xs"
                                    >
                                        {{ order.status }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-base"
                            >Work Orders Recentes</CardTitle
                        >
                        <CardDescription
                            >Últimas 5 ordens de trabalho</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-if="
                                    recent_activities.work_orders.length === 0
                                "
                                class="py-4 text-center text-sm text-muted-foreground"
                            >
                                Nenhuma work order ainda
                            </div>
                            <div
                                v-for="wo in recent_activities.work_orders"
                                :key="wo.id"
                                class="flex items-center justify-between border-b pb-2 last:border-0"
                            >
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium">{{
                                        wo.title
                                    }}</span>
                                    <span class="text-xs text-muted-foreground">
                                        {{
                                            wo.client?.name ||
                                            'Cliente não definido'
                                        }}
                                    </span>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <Badge
                                        :variant="
                                            wo.priority === 'high'
                                                ? 'destructive'
                                                : wo.priority === 'medium'
                                                  ? 'default'
                                                  : 'outline'
                                        "
                                        class="text-xs"
                                    >
                                        {{ wo.priority }}
                                    </Badge>
                                    <Badge
                                        :variant="
                                            wo.status === 'in_progress'
                                                ? 'default'
                                                : 'outline'
                                        "
                                        class="text-xs"
                                    >
                                        {{ wo.status }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card class="col-span-full">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <DollarSign class="h-5 w-5" />
                        Resumo Financeiro
                    </CardTitle>
                    <CardDescription>Visão geral das finanças</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-6 md:grid-cols-3">
                        <div class="space-y-2">
                            <h4
                                class="text-sm font-semibold text-muted-foreground"
                            >
                                Faturas Clientes
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Total</span>
                                    <span class="font-medium">{{
                                        stats.financials.customer_invoices.total
                                    }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Pendentes</span>
                                    <Badge
                                        variant="outline"
                                        class="bg-blue-50 text-blue-700"
                                    >
                                        {{
                                            stats.financials.customer_invoices
                                                .pending
                                        }}
                                    </Badge>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Pagas</span>
                                    <Badge
                                        variant="outline"
                                        class="bg-green-50 text-green-700"
                                    >
                                        {{
                                            stats.financials.customer_invoices
                                                .paid
                                        }}
                                    </Badge>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Atrasadas</span>
                                    <Badge variant="destructive">
                                        {{
                                            stats.financials.customer_invoices
                                                .overdue
                                        }}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h4
                                class="text-sm font-semibold text-muted-foreground"
                            >
                                Faturas Fornecedores
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Total</span>
                                    <span class="font-medium">{{
                                        stats.financials.supplier_invoices.total
                                    }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">A Pagar</span>
                                    <Badge
                                        variant="outline"
                                        class="bg-orange-50 text-orange-700"
                                    >
                                        {{
                                            stats.financials.supplier_invoices
                                                .pending
                                        }}
                                    </Badge>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Pagas</span>
                                    <Badge
                                        variant="outline"
                                        class="bg-green-50 text-green-700"
                                    >
                                        {{
                                            stats.financials.supplier_invoices
                                                .paid
                                        }}
                                    </Badge>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Atrasadas</span>
                                    <Badge variant="destructive">
                                        {{
                                            stats.financials.supplier_invoices
                                                .overdue
                                        }}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h4
                                class="text-sm font-semibold text-muted-foreground"
                            >
                                Resumo
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Receita Total</span>
                                    <span class="font-bold text-green-600">{{
                                        format(stats.financials.revenue.total)
                                    }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Despesas Totais</span>
                                    <span class="font-bold text-red-600">{{
                                        format(stats.financials.expenses.total)
                                    }}</span>
                                </div>
                                <div class="my-2 h-px bg-border"></div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold"
                                        >Lucro</span
                                    >
                                    <span
                                        class="text-lg font-bold"
                                        :class="
                                            profit >= 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{ format(profit) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-muted-foreground"
                                        >Pendente Receber</span
                                    >
                                    <span class="text-xs font-medium">{{
                                        format(stats.financials.revenue.pending)
                                    }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-muted-foreground"
                                        >Pendente Pagar</span
                                    >
                                    <span class="text-xs font-medium">{{
                                        format(
                                            stats.financials.expenses.pending,
                                        )
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="grid gap-4 md:grid-cols-2">
                <Card
                    v-if="
                        stats.financials.customer_invoices.overdue > 0 ||
                        stats.financials.supplier_invoices.overdue > 0
                    "
                >
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-red-600">
                            <AlertCircle class="h-5 w-5" />
                            Alertas
                        </CardTitle>
                        <CardDescription
                            >Itens que requerem atenção</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-if="
                                    stats.financials.customer_invoices.overdue >
                                    0
                                "
                                class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-950"
                            >
                                <AlertCircle
                                    class="mt-0.5 h-5 w-5 text-red-600"
                                />
                                <div>
                                    <p
                                        class="text-sm font-medium text-red-900 dark:text-red-100"
                                    >
                                        {{
                                            stats.financials.customer_invoices
                                                .overdue
                                        }}
                                        fatura(s) de clientes atrasada(s)
                                    </p>
                                    <p
                                        class="text-xs text-red-700 dark:text-red-300"
                                    >
                                        Contactar clientes para pagamento
                                    </p>
                                </div>
                            </div>

                            <div
                                v-if="
                                    stats.financials.supplier_invoices.overdue >
                                    0
                                "
                                class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-950"
                            >
                                <AlertCircle
                                    class="mt-0.5 h-5 w-5 text-red-600"
                                />
                                <div>
                                    <p
                                        class="text-sm font-medium text-red-900 dark:text-red-100"
                                    >
                                        {{
                                            stats.financials.supplier_invoices
                                                .overdue
                                        }}
                                        fatura(s) de fornecedores atrasada(s)
                                    </p>
                                    <p
                                        class="text-xs text-red-700 dark:text-red-300"
                                    >
                                        Processar pagamentos urgentemente
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Activity class="h-5 w-5" />
                            Atividade
                        </CardTitle>
                        <CardDescription>Resumo de atividades</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                class="flex items-center justify-between rounded-md bg-accent/50 p-2"
                            >
                                <div class="flex items-center gap-2">
                                    <Briefcase
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <span class="text-sm"
                                        >Work Orders Pendentes</span
                                    >
                                </div>
                                <Badge variant="outline">
                                    {{ stats.work_orders.pending }}
                                </Badge>
                            </div>

                            <div
                                class="flex items-center justify-between rounded-md bg-accent/50 p-2"
                            >
                                <div class="flex items-center gap-2">
                                    <Activity
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <span class="text-sm"
                                        >Work Orders Em Progresso</span
                                    >
                                </div>
                                <Badge variant="default">
                                    {{ stats.work_orders.in_progress }}
                                </Badge>
                            </div>

                            <div
                                class="flex items-center justify-between rounded-md bg-accent/50 p-2"
                            >
                                <div class="flex items-center gap-2">
                                    <FileText
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <span class="text-sm"
                                        >Propostas em Rascunho</span
                                    >
                                </div>
                                <Badge variant="outline">
                                    {{ stats.sales.draft_proposals }}
                                </Badge>
                            </div>

                            <div
                                class="flex items-center justify-between rounded-md bg-accent/50 p-2"
                            >
                                <div class="flex items-center gap-2">
                                    <ShoppingCart
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <span class="text-sm"
                                        >Encomendas em Rascunho</span
                                    >
                                </div>
                                <Badge variant="outline">
                                    {{ stats.sales.draft_orders }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
