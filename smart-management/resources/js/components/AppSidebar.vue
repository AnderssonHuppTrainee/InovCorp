<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import articles from '@/routes/articles';
import bankAccounts from '@/routes/bank-accounts';
import calendar from '@/routes/calendar';
import contacts from '@/routes/contacts';
import customerInvoices from '@/routes/customer-invoices';
import digitalArchive from '@/routes/digital-archive';
import entities from '@/routes/entities';
import logs from '@/routes/logs';
import orders from '@/routes/orders';
import proposals from '@/routes/proposals';
import roles from '@/routes/roles';
import taxRates from '@/routes/tax-rates';
import company from '@/routes/settings/company';
import supplierInvoices from '@/routes/supplier-invoices';
import users from '@/routes/users';
import workOrders from '@/routes/work-orders';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clientes',
        href: entities.index().url + '?type=client',
        icon: LayoutGrid,
    },
    {
        title: 'Fornecedores',
        href: entities.index().url + '?type=supplier',
        icon: LayoutGrid,
    },
    {
        title: 'Contactos',
        href: contacts.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Propostas',
        href: proposals.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Encomendas',
        href: orders.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Calendario',
        href: calendar.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Ordens de Trabalho',
        href: workOrders.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Contas Bancárias',
        href: bankAccounts.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Conta Corrente Clientes',
        href: customerInvoices.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Faturas Fornecedores',
        href: supplierInvoices.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Arquivo Digital',
        href: digitalArchive.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Gestão de Acessos',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Gestão de Users',
        href: users.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Gestão de Permissões',
        href: roles.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Artigos',
        href: articles.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Company',
        href: company.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Configurações',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Taxas de Imposto',
        href: taxRates.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Logs',
        href: logs.index().url,
        icon: LayoutGrid,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
