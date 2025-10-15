import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    items?: NavItem[];
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Entity {
    id: number;
    number?: string;
    tax_number: string;
    name: string;
    types: string[];
    address: string;
    postal_code: string;
    city: string;
    country_id: number;
    country?: {
        id: number;
        name: string;
        code: string;
    };
    phone: string | null;
    mobile: string | null;
    website: string | null;
    email: string | null;
    gdpr_consent: boolean;
    observations: string | null;
    status: 'active' | 'inactive';
    created_at: string;
    updated_at: string;
}


export interface Country {
  id: number
  name: string
  code: string
  phone_code: string
  is_active : boolean
}

export interface CalendarEvent {
    id: string | number;
    title: string;
    start: string;
    end: string;
    backgroundColor?: string;
    borderColor?: string;
    extendedProps?: {
        entity_id: number | null;
        entity_name: string | null;
        type_name: string | null;
        action_name: string | null;
        status: string;
        knowledge: boolean;
    };
}