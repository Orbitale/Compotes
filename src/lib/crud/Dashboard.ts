import { DashboardDefinition, UrlAction } from '@orbitale/svelte-admin';
import Home from 'carbon-icons-svelte/lib/Home.svelte';

import OperationCrud from '$lib/crud/cruds/OperationCrud';
import BankAccountsCrud from "$lib/crud/cruds/BankAccountsCrud";
import TagRulesCrud from "$lib/crud/cruds/TagRulesCrud";

export const dashboard = new DashboardDefinition({
    adminConfig: {
        defaultLocale: 'en',
        rootUrl: '/crud/',
        head: {
            brandName: 'Compotes',
            appName: ''
        }
    },

    sideMenu: [
        new UrlAction('Homepage', '/', Home),
        // new UrlAction('Analytics', '/analytics'),
        new UrlAction('Operations', '/crud/operations/list'),
        new UrlAction('Tag rules', '/crud/tag-rules/list'),
        // new UrlAction('Tags', '/crud/tags/list'),
        // new UrlAction('Triage', '/crud/triage/list'),
        new UrlAction('Bank accounts', '/crud/bank-accounts/list'),
        new UrlAction('Import', '/import'),
    ],

    cruds: [
        OperationCrud,
        BankAccountsCrud,
        TagRulesCrud,
    ]
});
