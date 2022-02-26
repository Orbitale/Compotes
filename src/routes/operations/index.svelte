<script lang="ts">
    import {getOperations, operationsStore} from "$lib/db/operations.ts";
    import Field from "$lib/struct/Field.ts";
    import FieldHtmlProperties from "$lib/struct/FieldHtmlProperties.ts";
    import CollectionField from "$lib/struct/CollectionField";
    import {onMount} from "svelte";
    import PaginatedTable from "$lib/admin/PaginatedTable/PaginatedTable.svelte";
    import PageHook from "$lib/struct/PageHook";

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

    onMount(async () => {
        await getOperations(1);
    });

    const changePageHook = new PageHook(getOperations);
</script>

<h1>Operations</h1>

<PaginatedTable items_store={operationsStore} fields={fields} changePageHook={changePageHook} />
