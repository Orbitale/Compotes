import { DashboardDefinition, UrlAction } from '@orbitale/svelte-admin';
import Home from 'carbon-icons-svelte/lib/Home.svelte';
import PiggyBank from 'carbon-icons-svelte/lib/PiggyBank.svelte';
import ChartLogisticRegression from 'carbon-icons-svelte/lib/ChartLogisticRegression.svelte';
import ListBoxes from 'carbon-icons-svelte/lib/ListBoxes.svelte';
import Tag from 'carbon-icons-svelte/lib/Tag.svelte';
import TagGroup from 'carbon-icons-svelte/lib/TagGroup.svelte';
import FetchUpload from 'carbon-icons-svelte/lib/FetchUpload.svelte';
import ListCheckedMirror from 'carbon-icons-svelte/lib/ListCheckedMirror.svelte';

import OperationCrud from '$lib/crud/cruds/OperationCrud';
import BankAccountsCrud from '$lib/crud/cruds/BankAccountsCrud';
import TagRulesCrud from '$lib/crud/cruds/TagRulesCrud';
import TagsCrud from '$lib/crud/cruds/TagsCrud';
import TriageCrud from '$lib/crud/cruds/TriageCrud';

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
		// new UrlAction('Analytics', '/analytics', ChartLogisticRegression),
		new UrlAction('Operations', '/crud/operations/list', ListBoxes),
		new UrlAction('Tags', '/crud/tags/list', Tag),
		new UrlAction('Tag rules', '/crud/tag-rules/list', TagGroup),
		new UrlAction('Triage', '/crud/triage/list', ListCheckedMirror),
		new UrlAction('Bank accounts', '/crud/bank-accounts/list', PiggyBank),
		new UrlAction('Import', '/import', FetchUpload)
	],

	cruds: [OperationCrud, BankAccountsCrud, TagRulesCrud, TagsCrud, TriageCrud]
});
