<script lang="ts">
    import {needsUser} from '../auth/current_user.ts';
    import {getBankAccounts} from "../db/bank_accounts.ts";
    import PaginatedTable from "../components/PaginatedTable/PaginatedTable.svelte";
    import {onMount} from "svelte";
    import EmptyCollection from "../components/PaginatedTable/EmptyCollection.svelte";
    import Field from "../struct/Field.ts";

    needsUser();

    let bank_accounts = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('name', 'Name'),
        new Field('slug', 'Slug'),
        new Field('currency', 'Currency'),
    ];

    onMount(async () => bank_accounts = await getBankAccounts());
</script>

{#if bank_accounts.length}
    <PaginatedTable items={bank_accounts} fields={fields} />
{:else}
    <EmptyCollection />
{/if}
