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
  id?: number
  tax_number?: string
  name?: string
  types?: string[]
  address?: string
  postal_code?: string
  city?: string
  country_id?: number | string
  phone?: string
  mobile?: string
  website?: string
  email?: string
  gdpr_consent?: boolean
  observations?: string
  status?: 'active' | 'inactive'
}

interface Country {
  id: number
  name: string
  code: string
  phone_code: string
  is_active : boolean
}