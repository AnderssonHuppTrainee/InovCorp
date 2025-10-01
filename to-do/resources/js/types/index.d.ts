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
//tipar task
export interface Task {
  id: number;
  title: string;
  description: string | null;
  priority: 'low' | 'medium' | 'high';
  due_date: string | null;
  status: 'pending' | 'completed';
  created_at?: string;
  updated_at?: string;
}

// typar filtros
export type Filters = {
  search?: string;
  status?: ''|'pending' | 'completed';
  priority?:''| 'low' | 'medium' | 'high';
  due_from?: string;
  due_to?: string;
  sort_by?: 'due_date' | 'priority' | 'title' | 'created_at';
  sort_dir?: 'asc' | 'desc';
  per_page?: number;
};


export type BreadcrumbItemType = BreadcrumbItem;
