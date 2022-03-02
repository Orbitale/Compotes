<script lang="ts">
    import {getOperations, getOperationsCount, operationsStore} from "$lib/db/operations.ts";
    import {onMount} from "svelte";
    import PaginatedTable from "$lib/admin/components/PaginatedTable/PaginatedTable.svelte";
    import CollectionField from "$lib/admin/CollectionField";
    import Field from "$lib/admin/Field";
    import FieldHtmlProperties from "$lib/admin/FieldHtmlProperties";
    import PageHooks from "$lib/admin/PageHooks";

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

    const pageHooks = new PageHooks(getOperations, getOperationsCount);
</script>

<h1>Operations</h1>

<PaginatedTable items_store={operationsStore} fields={fields} pageHooks={pageHooks} />
