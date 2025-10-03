<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import routeTasks from '@/routes/tasks';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowDown,
    Calendar,
    CheckCircle2,
    CheckSquare,
    Circle,
    CircleAlert,
    ClipboardList,
    Clock,
    Flag,
    Hourglass,
} from 'lucide-vue-next';

const props = defineProps<{
    stats: {
        total: number;
        completed: number;
        pending: number;
        overdue: number;
    };
    recentTasks: Array<{
        id: number;
        title: string;
        description: string;
        priority: string;
        due_date: string;
        status: string;
        category: string;
        created_at: string;
        updated_at: string;
    }>;
    weeklyProgress: Array<{
        day: string;
        tasks: number;
        percentage: number;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// dados das estatisticas
const statsData = [
    {
        title: 'Tarefas Totais',
        value: props.stats.total.toString(),
        change: '+12%',
        trend: 'up',
        icon: ClipboardList,
        color: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
    },
    {
        title: 'Concluídas',
        value: props.stats.completed.toString(),
        change: '+8%',
        trend: 'up',
        icon: CheckCircle2,
        color: 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400',
    },
    {
        title: 'Pendentes',
        value: props.stats.pending.toString(),
        change: '-3%',
        trend: 'down',
        icon: Clock,
        color: 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400',
    },
    {
        title: 'Atrasadas',
        value: props.stats.overdue.toString(),
        change: '+1%',
        trend: 'up',
        icon: AlertTriangle,
        color: 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
    },
];

const priorityColors: Record<
    string,
    { label: string; icon: any; color: string }
> = {
    high: {
        label: 'Alta',
        icon: Flag,
        color: 'bg-red-500 text-white dark:bg-red-900/30 dark:text-red-300',
    },
    medium: {
        label: 'Média',
        icon: CircleAlert,
        color: 'bg-yellow-300 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    },
    low: {
        label: 'Baixa',
        icon: ArrowDown,
        color: 'bg-green-500 text-white dark:bg-green-900/30 dark:text-green-300',
    },
};
const categoryColors: Record<string, string> = {
    Geral: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
};

const statusIcons: Record<string, any> = {
    completed: CheckSquare,
    pending: Hourglass,
    in_progress: Circle,
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-BR');
};

const isOverdue = (dueDate: string, status: string) => {
    return new Date(dueDate) < new Date() && status !== 'completed';
};

const getStatusIcon = (status: string) => {
    return statusIcons[status] || ClipboardList;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Visão Geral
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Bem-vindo de volta! Aqui está o resumo das suas tarefas.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="stat in statsData"
                    :key="stat.title"
                    class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400"
                            >
                                {{ stat.title }}
                            </p>
                            <p
                                class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white"
                            >
                                {{ stat.value }}
                            </p>
                        </div>
                        <div :class="['rounded-lg p-3', stat.color]">
                            <component
                                :is="stat.icon"
                                class="h-6 w-6"
                                :class="
                                    stat.color.includes('text-')
                                        ? ''
                                        : 'text-current'
                                "
                            />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span
                            :class="[
                                'text-sm font-medium',
                                stat.trend === 'up'
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-red-600 dark:text-red-400',
                            ]"
                        >
                            {{ stat.change }}
                        </span>
                        <span
                            class="ml-2 text-sm text-gray-500 dark:text-gray-400"
                        >
                            vs último mês
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-gray-800"
                >
                    <div
                        class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Tarefas Recentes
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Suas tarefas mais recentes e pendentes
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div
                                v-for="task in recentTasks"
                                :key="task.id"
                                class="flex items-center justify-between rounded-lg border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
                            >
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <component
                                            :is="getStatusIcon(task.status)"
                                            class="h-5 w-5"
                                            :class="
                                                task.status === 'completed'
                                                    ? 'text-green-500'
                                                    : task.status ===
                                                        'in_progress'
                                                      ? 'text-blue-500'
                                                      : 'text-gray-400'
                                            "
                                        />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p
                                            :class="[
                                                'truncate font-medium',
                                                task.status === 'completed'
                                                    ? 'text-gray-400 line-through dark:text-gray-500'
                                                    : 'text-gray-900 dark:text-white',
                                            ]"
                                        >
                                            {{ task.title }}
                                        </p>
                                        <div
                                            class="mt-1 flex items-center space-x-2"
                                        >
                                            <span
                                                v-if="
                                                    priorityColors[
                                                        task.priority
                                                    ]
                                                "
                                                class="mt-2 inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold text-white sm:mt-0"
                                                :class="
                                                    priorityColors[
                                                        task.priority
                                                    ].color
                                                "
                                                :aria-label="`Prioridade ${priorityColors[task.priority].label}`"
                                                role="status"
                                            >
                                                <component
                                                    :is="
                                                        priorityColors[
                                                            task.priority
                                                        ].icon
                                                    "
                                                    class="h-3 w-3"
                                                />
                                                {{
                                                    priorityColors[
                                                        task.priority
                                                    ].label
                                                }}
                                            </span>
                                            <span
                                                :class="[
                                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                                    categoryColors[
                                                        task.category
                                                    ] ||
                                                        categoryColors['Geral'],
                                                ]"
                                            >
                                                {{ task.category }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="flex flex-shrink-0 items-center space-x-2 text-right"
                                >
                                    <Calendar class="h-4 w-4 text-gray-400" />
                                    <div>
                                        <p
                                            class="text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            {{ formatDate(task.due_date) }}
                                        </p>
                                        <p
                                            :class="[
                                                'text-xs',
                                                isOverdue(
                                                    task.due_date,
                                                    task.status,
                                                )
                                                    ? 'text-red-500'
                                                    : 'text-gray-500',
                                            ]"
                                        >
                                            {{
                                                isOverdue(
                                                    task.due_date,
                                                    task.status,
                                                )
                                                    ? 'Atrasada'
                                                    : task.status ===
                                                        'completed'
                                                      ? 'Concluída'
                                                      : 'Prazo'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <Link
                            :href="routeTasks.index().url"
                            class="mt-6 flex w-full items-center justify-center rounded-lg border border-sidebar-border/70 bg-transparent px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-sidebar-border dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Ver todas as tarefas
                        </Link>
                    </div>
                </div>

                <div
                    class="rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-gray-800"
                >
                    <div
                        class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Progresso Semanal
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Seu desempenho na última semana
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div
                                v-for="day in weeklyProgress"
                                :key="day.day"
                                class="flex items-center justify-between"
                            >
                                <span
                                    class="w-8 text-sm font-medium text-gray-600 dark:text-gray-400"
                                    >{{ day.day }}</span
                                >
                                <div class="mx-4 flex-1">
                                    <div
                                        class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700"
                                    >
                                        <div
                                            class="h-2 rounded-full bg-green-500 transition-all duration-300"
                                            :style="{
                                                width: `${day.percentage}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <span
                                    class="w-12 text-right text-sm text-gray-600 dark:text-gray-400"
                                >
                                    {{ day.tasks }} tasks
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div
                                class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20"
                            >
                                <p
                                    class="text-sm font-medium text-blue-600 dark:text-blue-400"
                                >
                                    Taxa de Conclusão
                                </p>
                                <p
                                    class="text-2xl font-bold text-blue-900 dark:text-blue-100"
                                >
                                    {{
                                        stats.total > 0
                                            ? Math.round(
                                                  (stats.completed /
                                                      stats.total) *
                                                      100,
                                              )
                                            : 0
                                    }}%
                                </p>
                            </div>
                            <div
                                class="rounded-lg bg-purple-50 p-4 dark:bg-purple-900/20"
                            >
                                <p
                                    class="text-sm font-medium text-purple-600 dark:text-purple-400"
                                >
                                    Tarefas Hoje
                                </p>
                                <p
                                    class="text-2xl font-bold text-purple-900 dark:text-purple-100"
                                >
                                    {{
                                        weeklyProgress[
                                            weeklyProgress.length - 1
                                        ]?.tasks || 0
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
