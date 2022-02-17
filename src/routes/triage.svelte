<script lang="ts">
    import AdminTable from "$lib/admin/AdminTable.svelte";
    import Field from "$lib/struct/Field";
    import FieldHtmlProperties from "$lib/struct/FieldHtmlProperties";
    import UrlAction from "$lib/struct/UrlAction";
    import ActionParams from "$lib/struct/ActionParams";
    import {onMount} from "svelte";
    import {getTriageOperations, deleteOperation, triageStore} from "$lib/db/operations";
    import Operation from "$lib/entities/Operation";
    import CallbackAction from "$lib/struct/CallbackAction";
    import {success} from "$lib/utils/message";

    let triaged_operations: Operation[] = [];

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

    triageStore.subscribe((ops: Operation[]) => {
        triaged_operations = ops;
    });

    async function doDeleteOperation(operation: Operation) {
        const id = operation.id;
        if (!(await confirm('Are you sure?'))) {
            return;
        }
        await deleteOperation(operation);
        success(`Successfully deleted operation with id ${id}!`);
    }
</script>

<h1>Triage</h1>

<AdminTable items={triaged_operations} fields={fields} actions={actions} />
