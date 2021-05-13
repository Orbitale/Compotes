<script lang="ts">
    import Operation from "../../entities/Operation";
    import {getBankAccountById} from "../../db/bank_accounts.ts";
    import {onMount} from "svelte";

    export let operation: Operation;

    let date = new Date(operation.operation_date);
    let date_as_string = date.toLocaleDateString();
    let bank_account = {name: "…"};

    onMount(async () => {
        bank_account = await getBankAccountById(operation.bank_account_id.toString());
    });
</script>

<style lang="scss">
    .operation-date,
    .operation-bank-account
    {
        white-space: nowrap;
    }
</style>

<tr>
    <td>{operation.id}</td>
    <td class="operation-date">{date_as_string}</td>
    <td class="operation-bank-account">{bank_account.name}</td>
    <td>{operation.op_type}</td>
    <td>{operation.details}</td>
    <td>{operation.amount_in_cents/100}</td>
    <td>
        {#if operation.ignored_from_charts}
            ✔
        {:else}
            ❌
        {/if}
    </td>
    <td>&ndash;</td>
</tr>
