import { Link, usePage } from '@inertiajs/react';
import type { SharedPageProps } from '../types';

export function CsrfField() {
    const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

    return <input name="_token" type="hidden" value={token} />;
}

export function FieldError({ name }: { name: string }) {
    const message = usePage<SharedPageProps>().props.errors[name];

    return message ? <span className="mt-1 block text-xs text-danger">{message}</span> : null;
}

export function Pagination({ currentPage, lastPage, path }: { currentPage: number; lastPage: number; path: string }) {
    if (lastPage <= 1) {
        return null;
    }

    const pages = Array.from({ length: lastPage }, (_, index) => index + 1);

    return (
        <nav aria-label="Pagination" className="mt-6 flex flex-wrap gap-2">
            {pages.map((page) => (
                <Link
                    aria-current={page === currentPage ? 'page' : undefined}
                    className={`rounded-md border px-3 py-2 text-sm ${page === currentPage ? 'border-brand bg-brand text-white' : 'border-border bg-white text-foreground hover:bg-muted'}`}
                    href={`${path}?page=${page}`}
                    key={page}
                >
                    {page}
                </Link>
            ))}
        </nav>
    );
}

export function formatDateTime(value: string, timeZone: string) {
    const date = new Date(value);
    const parts = new Intl.DateTimeFormat('en-CA', {
        day: '2-digit',
        hour: '2-digit',
        hourCycle: 'h23',
        minute: '2-digit',
        month: '2-digit',
        timeZone,
        year: 'numeric',
    }).formatToParts(date);
    const fields = Object.fromEntries(parts.map(({ type, value: part }) => [type, part]));

    return {
        date: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', timeZone, year: 'numeric' }),
        time: date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', timeZone }),
        localInput: `${fields.year}-${fields.month}-${fields.day}T${fields.hour}:${fields.minute}`,
    };
}

export function formatMoney(value: number | string) {
    return `₱${Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}
