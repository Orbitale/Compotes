import Home from 'carbon-icons-svelte/lib/Home.svelte';

import { DashboardDefinition, UrlAction } from '@orbitale/svelte-admin';

export const dashboard = new DashboardDefinition({
    adminConfig: {
        defaultLocale: 'en',
        rootUrl: '/',
        head: {
            brandName: 'Compotes',
            appName: ''
        }
    },

    sideMenu: [
        new UrlAction('Homepage', '/', Home),
        new UrlAction('Analytics', '/analytics'),
        new UrlAction('Operations', '/operations'),
        new UrlAction('Tag rules', '/tag-rules'),
        new UrlAction('Tags', '/tags'),
        new UrlAction('Triage', '/triage'),
        new UrlAction('Bank accounts', '/bank-accounts'),
        new UrlAction('Import', '/import'),
    ],

    cruds: []
});
