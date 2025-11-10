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

export interface Item {
    article_id: string | number;
    article?: {
        id: number;
        reference: string;
        name: string;
    };
    supplier_id?: string | number;
    supplier?: {
        id: number;
        name: string;
    };
    quantity: number;
    unit_price: number;
    cost_price: number | null;
    notes?: string | null;
}

export interface Proposal {
        id: number;
        number: string;
        proposal_date: string;
        client_id: number;
        client?: {
            id: number;
            name: string;
        };
        validity_date: string;
        total_amount: number;
        status: 'draft' | 'closed';
        items: ProposalItem[];
        created_at: string;
        updated_at: string;
}

export interface ProposalItem {
    id: number;
    article_id: string | number;
    article: Article;
    supplier_id?: string | number;
    supplier?: {
        id: number;
        name: string;
    };
    quantity: number;
    unit_price: number;
    cost_price: number | null;
    notes?: string | null;
    created_at: string;
    updated_at: string;
}

export interface Order {
    id:number;
    number: string;
    order_date: string;
    delivery_date: string | null;
    client_id: number;
    client?: {
        id: number;
        name: string;
    };
    proposal_id: number;
    proposal?: {
        id:number;
        number: string;
    }
    total_amount: number;
    status: 'draft' | 'closed';
    items: Item[];
    created_at: string;
    updated_at: string;
}

export interface Country {
  id: number
  name: string
  code: string
  phone_code: string
  is_active : boolean
  created_at: string
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

export type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    from: number;
    to: number;
    prev_page_url: string | null;
    next_page_url: string | null;
};


export interface Article {
    id: number;
    reference: string;
    name: string;
    price: number;
    tax_rate_id: number;
}

export interface BankAccount {
    id:number;
    name: string;
    account_number: string;
    iban: string;
    swift: string | null;
    bank_name: string;
    balance: number | null;
    currency: string | null;
    is_active: boolean;
    created_at : string | null;
    updated_at: string | null;
    deleted_at: string | null;
} 

export interface Contact {
    id: number;
    number: string;
    first_name: string;
    last_name: string;
    entity_id: number;
    entity?: {
        id: number;
        name: string;
    };
    role?: {
        id: number;
        name: string;
    };
    phone: string | null;
    mobile: string | null;
    email: string | null;
    gdpr_consent: boolean;
    observations: string | null;
    status: 'active' | 'inactive';
    created_at: string;
    updated_at: string;
}

export interface ContactRole {
    id: number;
    name: string;
    description: string;
    created_at: string | null;
    updated_at: string | null;
}
export interface Customer {
    id: number;
    name: string;
}

export interface SupplierOrder {
    id: number;
    number: string;
    order_date: string;
    supplier: {
        id: number;
        name: string;
    };
    order?: Order;
    total_amount: number;
    status: string;
    invoices?: Array<any>;
    created_at: string;
    updated_at: string;
}

export interface WorkOrder {
    id: number;
    number: string;
    title: string;
    description: string | null;
    priority: string;
    status: string;
    client_id: number;
    assigned_to: number | null;
    start_date: string | null;
    end_date: string | null;
    status: string;
    created_at: string;
    updated_at: string;
    client:{
        id:number;
        name: string;
    };
    assigned_user?:{
        id:number;
        name: string;
    }
}