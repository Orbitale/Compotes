<script lang="ts">
    import {needsUser} from '../auth/current_user.ts';
    import {getOperations} from "../db/operations.ts";
    import PaginatedTable from "../components/PaginatedTable/PaginatedTable.svelte";
    import Operation from "../entities/Operation.ts";

    needsUser();

    let operations = [];

    let fields = [
        {name: 'id', text: 'ID'},
        {name: 'operation_date', text: 'Date'},
        {name: 'bank_account_id', text: 'Bank account'},
        {name: 'op_type', text: 'Type'},
        {name: 'details', text: 'Details'},
        {name: 'amount_in_cents', text: 'Amount'},
        {name: 'ignored_from_charts', text: 'Ignored from charts'},
        {name: 'tags', text: 'Tags'},
    ];

    getOperations()
        .then((awaited_operations: Operation[]) => {
            let number_of_operations = awaited_operations.length;
            let current_operations = 0;
            operations = awaited_operations;

            awaited_operations.forEach((operation: Operation) => {

            });
        })
    ;
</script>

{#if operations.length}
    <PaginatedTable bind:items={operations} fields={fields} />
{:else}
    <div class="alert alert-warning">
        No operations yet.
    </div>
{/if}
