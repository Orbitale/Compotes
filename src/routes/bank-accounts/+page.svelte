<script lang="ts">
	import { getBankAccounts, bankAccountsStore } from '$lib/db/bank_accounts.ts';
	import PaginatedTable from '$lib/admin/components/PaginatedTable/PaginatedTable.svelte';
	import Field from '$lib/admin/src/Field';
	import PageHooks from '$lib/admin/src/PageHooks';
	import UrlAction from '$lib/admin/src/UrlAction';
	import ActionParams from '$lib/admin/src/ActionParams';

	let bank_accounts = [];

	let fields = [
		new Field('id', 'ID'),
		new Field('name', 'Name'),
		new Field('slug', 'Slug'),
		new Field('currency', 'Currency')
	];

	let actions = [new UrlAction('Edit', '/bank-accounts/edit/:id', ActionParams.id())];

	const pageHooks = new PageHooks(getBankAccounts);
</script>

<a href="/bank-accounts/new" class="btn btn-primary" id="new-button">New</a>

<h1>Bank accounts</h1>

<PaginatedTable
	id="bank_accounts"
	items_store={bankAccountsStore}
	{actions}
	{fields}
	page_hooks={pageHooks}
/>

<style lang="scss">
	#new-button {
		float: right;
		margin-top: 8px;
	}
</style>
