<script lang="ts">
    import {needsUser} from '../auth/current_user.ts';
    import {getOperations} from "../db/operations.ts";
    import PaginatedTable from "../components/PaginatedTable/PaginatedTable.svelte";
    import Operation from "../entities/Operation.ts";
    import FieldToDisplay from "../struct/FieldToDisplay.ts";
    import EmptyCollection from "../components/PaginatedTable/EmptyCollection.svelte";

    needsUser();

    let operations: Operation[] = [];

    let fields = [
        new FieldToDisplay('id', 'ID'),
        new FieldToDisplay('operation_date', 'Date'),
        new FieldToDisplay('bank_account', 'Bank account', new FieldToDisplay('name')),
        new FieldToDisplay('op_type', 'Type'),
        new FieldToDisplay('details', 'Details'),
        new FieldToDisplay('amount_in_cents', 'Amount'),
        new FieldToDisplay('ignored_from_charts', 'Ignored from charts'),
        new FieldToDisplay('tags', 'Tags'),
    ];

    getOperations()
        .then((awaited_operations: Operation[]) => {
            let number_of_operations = awaited_operations.length;
            let current_operations = 0;
            let pending_operations: Operation[] = [];

            awaited_operations.forEach((operation: Operation) => {
                operation.sync().then(() => {
                    pending_operations.push(operation);
                    current_operations++;
                    if (current_operations === number_of_operations) {
                        operations = pending_operations;
                    }
                });
            });
        })
    ;
</script>

{#if operations.length}
    <div class="alert alert-info">
        Lines having a <span class="badge bg-warning">colored background</span> correspond to operations that have no tag associated.
        It is highly recommended to use the <i class="fa fa-plus"></i> icon to add new tags to them.
    </div>

    <PaginatedTable items={operations} fields={fields} />
{:else}
    <EmptyCollection />
{/if}
