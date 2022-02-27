<script lang="ts">
    import {getBankAccounts, bankAccountsStore} from "$lib/db/bank_accounts.ts";
    import {onMount} from "svelte";
    import Field from "$lib/struct/Field.ts";
    import PaginatedTable from "$lib/admin/PaginatedTable/PaginatedTable.svelte";

    let bank_accounts = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('name', 'Name'),
        new Field('slug', 'Slug'),
        new Field('currency', 'Currency'),
    ];

    onMount(async () => bank_accounts = await getBankAccounts(1));
</script>

<style lang="scss">
    #new-button {
        float: right;
        margin-top: 8px;
    }
</style>

<a href="/bank-accounts/new" class="btn btn-primary" id="new-button">New</a>

<h1>Bank accounts</h1>

<PaginatedTable items_store={bankAccountsStore} fields={fields} />
