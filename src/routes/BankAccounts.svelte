<script lang="ts">
    import {needsUser} from '../auth/current_user.ts';
    import {getBankAccounts} from "../db/bank_accounts.ts";
    import PaginatedTable from "../components/PaginatedTable/PaginatedTable.svelte";
    import {onMount} from "svelte";
    import EmptyCollection from "../components/PaginatedTable/EmptyCollection.svelte";

    needsUser();

    let bank_accounts = [];

    let fields = [
        {name: 'id', text: 'ID'},
        {name: 'name', text: 'Name'},
        {name: 'slug', text: 'Slug'},
        {name: 'currency', text: 'Currency'},
    ];

    onMount(async () => bank_accounts = await getBankAccounts());
</script>

{#if bank_accounts.length}
    <PaginatedTable items={bank_accounts} fields={fields} />
{:else}
    <EmptyCollection />
{/if}
