<script lang="ts">
    import {getBankAccounts, bankAccountsStore} from "$lib/db/bank_accounts.ts";
    import PaginatedTable from "$lib/admin/components/PaginatedTable/PaginatedTable.svelte";
    import Field from "$lib/admin/Field";
    import PageHooks from "$lib/admin/PageHooks";

    let bank_accounts = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('name', 'Name'),
        new Field('slug', 'Slug'),
        new Field('currency', 'Currency'),
    ];

    const pageHooks = new PageHooks(getBankAccounts);
</script>

<style lang="scss">
    #new-button {
        float: right;
        margin-top: 8px;
    }
</style>

<a href="/bank-accounts/new" class="btn btn-primary" id="new-button">New</a>

<h1>Bank accounts</h1>

<PaginatedTable items_store={bankAccountsStore} fields={fields} pageHooks={pageHooks} />
