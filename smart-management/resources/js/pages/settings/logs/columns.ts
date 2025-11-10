import { Badge } from '@/components/ui/badge';
import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';

export interface Log {
    id: number;
    log_name: string;
    description: string;
    created_at: string;
    causer?: {
        name: string;
    };
    properties?: {
        ip?: string;
    };
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-PT');
};

const formatTime = (dateString: string) => {
    return new Date(dateString).toLocaleTimeString('pt-PT', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

export const columns: ColumnDef<Log>[] = [
    {
        accessorKey: 'created_at',
        header: 'Data',
        cell: ({ row }) => {
            return formatDate(row.original.created_at);
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Hora',
        cell: ({ row }) => {
            return formatTime(row.original.created_at);
        },
    },
    {
        accessorKey: 'causer',
        header: 'Utilizador',
        cell: ({ row }) => {
            return row.original.causer?.name || 'Sistema';
        },
    },
    {
        accessorKey: 'log_name',
        header: 'Menu',
        cell: ({ row }) => {
            return h(Badge, { variant: 'outline' }, () => row.original.log_name || '-');
        },
    },
    {
        accessorKey: 'description',
        header: 'Ação',
    },
    {
        accessorKey: 'properties.ip',
        header: 'IP',
        cell: ({ row }) => {
            return h('span', { class: 'font-mono text-xs' }, row.original.properties?.ip || '-');
        },
    },
];


