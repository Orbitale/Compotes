<script lang="ts">
    import Field from "$lib/struct/Field";
    import FieldHtmlProperties from "$lib/struct/FieldHtmlProperties";
    import UrlAction from "$lib/struct/UrlAction";
    import ActionParams from "$lib/struct/ActionParams";
    import {onMount} from "svelte";
    import {getTriageOperations, deleteOperation, triageStore} from "$lib/db/operations";
    import Operation from "$lib/entities/Operation";
    import CallbackAction from "$lib/struct/CallbackAction";
    import {success} from "$lib/utils/message";
    import PaginatedTable from "$lib/admin/PaginatedTable/PaginatedTable.svelte";

    let fields = [
        new Field('id', 'ID'),
        new Field('date', 'Date'),
        new Field('bank_account', 'Bank account', new Field('name')),
        new Field('op_type', 'Type'),
        new Field('details', 'Details'),
        new Field('amount_display', 'Amount', null, new FieldHtmlProperties('operation-amount')),
    ];

    let actions = [
        new UrlAction('Edit', '/operations/edit/triage-:id', ActionParams.id()),
        new CallbackAction('Delete', doDeleteOperation),
    ];

    onMount(async () => {
        await getTriageOperations();
    });

    async function doDeleteOperation(operation: Operation) {
        const id = operation.id;
        if (!(await confirm(`Deleting operation with ID #${operation.id}.\nAre you sure?`))) {
            return;
        }
        await deleteOperation(operation);
        success(`Successfully deleted operation with id ${id}!`);
    }
</script>

<h1>Triage</h1>

<PaginatedTable items_store={triageStore} fields={fields} actions={actions} />
