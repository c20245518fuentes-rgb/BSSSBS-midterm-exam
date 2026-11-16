export type User = {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
};

export type CustomerProfile = {
    name: string;
    email: string;
    client?: { phone: string } | null;
};

export type Service = {
    id: number;
    name: string;
    category: string;
    duration_min: number;
    price: number | string;
    appointments?: Appointment[];
};

export type Stylist = {
    id: number;
    name: string;
    specialization: string;
};

export type Client = {
    id: number;
    name: string;
    phone: string;
};

export type BillingInvoice = {
    id: number;
    total_amount: number | string;
    payment_status: string;
};

export type Appointment = {
    id: number;
    client_id: number;
    stylist_id: number;
    service_id: number;
    appointment_datetime: string;
    status: string;
    client: Client;
    stylist: Stylist;
    service: Service;
    billing_invoice?: BillingInvoice | null;
    billingInvoice?: BillingInvoice | null;
};

export type Paginator<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    path: string;
};

export type SharedPageProps = {
    auth: { user: User | null };
    errors: Record<string, string>;
    flash: { success?: string | null; status?: string | null };
    old: Record<string, string | undefined>;
    timezone: string;
};

declare module '@inertiajs/core' {
    interface InertiaConfig {
        sharedPageProps: SharedPageProps;
        errorValueType: string;
    }
}
