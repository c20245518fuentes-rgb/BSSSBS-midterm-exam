import { createInertiaApp } from '@inertiajs/react';
import type { ResolvedComponent } from '@inertiajs/react';

const pages = import.meta.glob<{ default: ResolvedComponent }>('./Pages/**/*.tsx');

createInertiaApp({
    resolve: async (name) => {
        const path = `./Pages/${name}.tsx`;
        const loadPage = pages[path];

        if (!loadPage) {
            throw new Error(`Unknown Inertia page: ${name}`);
        }

        return (await loadPage()).default;
    },
    title: (title) => title || 'Maison Studio',
});
