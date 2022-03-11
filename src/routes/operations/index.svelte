<script lang="ts">
    import {getOperations, getOperationsCount, operationsStore} from "$lib/db/operations.ts";
    import PaginatedTable from "$lib/admin/components/PaginatedTable/PaginatedTable.svelte";
    import CollectionField from "$lib/admin/CollectionField";
    import Field from "$lib/admin/Field";
    import FieldHtmlProperties from "$lib/admin/FieldHtmlProperties";
    import PageHooks from "$lib/admin/PageHooks";
    import CallbackAction from "$lib/admin/CallbackAction";
    import Operation from "$lib/entities/Operation";
    import Modal, {getModal} from "$lib/modal/Modal.svelte";

    let operationId: number = null;

    let fields = [
        new Field('id', 'ID'),
        new Field('date', 'Date'),
        new Field('bank_account', 'Bank account', new Field('name')),
        new Field('op_type', 'Type'),
        new Field('details', 'Details'),
        new Field('amount_display', 'Amount', null, new FieldHtmlProperties('operation-amount')),
        new Field('ignored_from_charts', 'Ignored from charts'),
        new CollectionField('tags', 'Tags', new Field('name')),
    ];

    let actions = [
        new CallbackAction('Add tags', function(operation: Operation) {
            let tags_modal = getModal('tags_modal');
            if (!tags_modal) {
                console.warn('Modal "tags_modal" is not set.');
                return;
            }
            operationId = operation.id;
            tags_modal.open();
        }),
    ];

    const pageHooks = new PageHooks(getOperations, getOperationsCount);
</script>

<h1>Operations</h1>

<Modal id="tags_modal" title="Add tags">
    TODO: Let's add tags for operation ID {operationId}!
</Modal>

<PaginatedTable items_store={operationsStore} actions={actions} fields={fields} pageHooks={pageHooks} />
