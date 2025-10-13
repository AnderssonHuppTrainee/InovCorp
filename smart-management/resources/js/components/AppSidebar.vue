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
import calendarActions from '@/routes/calendar-actions';
import calendarEventTypes from '@/routes/calendar-event-types';
import contactRoles from '@/routes/contact-roles';
import contacts from '@/routes/contacts';
import countries from '@/routes/countries';
import customerInvoices from '@/routes/customer-invoices';
import digitalArchive from '@/routes/digital-archive';
import entities from '@/routes/entities';
import logs from '@/routes/logs';
import orders from '@/routes/orders';
import proposals from '@/routes/proposals';
import roles from '@/routes/roles';
import company from '@/routes/settings/company';
import supplierInvoices from '@/routes/supplier-invoices';
import supplierOrders from '@/routes/supplier-orders';
import taxRates from '@/routes/tax-rates';
import users from '@/routes/users';
import workOrders from '@/routes/work-orders';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    Building2,
    Calendar,
    ClipboardList,
    Contact,
    FileText,
    LayoutDashboard,
    Settings,
    ShieldCheck,
    ShoppingCart,
    Users,
    Wallet,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutDashboard,
    },
    {
        title: 'Clientes',
        href: entities.index().url + '?type=client',
        icon: Users,
    },
    {
        title: 'Fornecedores',
        href: entities.index().url + '?type=supplier',
        icon: Building2,
    },
    {
        title: 'Contactos',
        href: contacts.index().url,
        icon: Contact,
    },
    {
        title: 'Propostas',
        href: proposals.index().url,
        icon: FileText,
    },
    {
        title: 'Calendário',
        href: calendar.index().url,
        icon: Calendar,
    },
    {
        title: 'Encomendas',
        href: orders.index().url,
        icon: ShoppingCart,
        items: [
            {
                title: 'Clientes',
                href: orders.index().url,
            },
            {
                title: 'Fornecedores',
                href: supplierOrders.index().url,
            },
        ],
    },
    {
        title: 'Ordens de Trabalho',
        href: workOrders.index().url,
        icon: ClipboardList,
    },
    {
        title: 'Financeiro',
        href: bankAccounts.index().url,
        icon: Wallet,
        items: [
            {
                title: 'Contas Bancárias',
                href: bankAccounts.index().url,
            },
            {
                title: 'Conta Corrente Clientes',
                href: customerInvoices.index().url,
            },
            {
                title: 'Faturas Fornecedores',
                href: supplierInvoices.index().url,
            },
            {
                title: 'Arquivo Digital',
                href: digitalArchive.index().url,
            },
        ],
    },
    {
        title: 'Gestão de Acessos',
        href: users.index().url,
        icon: ShieldCheck,
        items: [
            {
                title: 'Utilizadores',
                href: users.index().url,
            },
            {
                title: 'Permissões',
                href: roles.index().url,
            },
        ],
    },
    {
        title: 'Configurações',
        href: articles.index().url,
        icon: Settings,
        items: [
            {
                title: 'Países',
                href: countries.index().url,
            },
            {
                title: 'Contactos - Funções',
                href: contactRoles.index().url,
            },
            {
                title: 'Calendário - Tipos',
                href: calendarEventTypes.index().url,
            },
            {
                title: 'Calendário - Acções',
                href: calendarActions.index().url,
            },
            {
                title: 'Artigos',
                href: articles.index().url,
            },
            {
                title: 'Financeiro - IVA',
                href: taxRates.index().url,
            },
            {
                title: 'Logs',
                href: logs.index().url,
            },
            {
                title: 'Empresa',
                href: company.index().url,
            },
        ],
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
