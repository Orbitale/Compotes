<script lang="ts">
    import Operation from "../../entities/Operation";

    export let operation: Operation;

    let date = new Date(operation.operation_date);
    let date_as_string = date.getFullYear()+"-"+date.getMonth()+"-"+date.getDay();
</script>

<tr>
    <td>
        {operation.id}
    </td>
    <td>{date_as_string}</td>
    <td>{operation.bank_account_id}</td>
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
