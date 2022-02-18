<script lang="ts">
    import {needsUser} from '$lib/auth/current_user.ts';
    import {getOperations, operationsStore} from "$lib/db/operations.ts";
    import Operation from "$lib/entities/Operation.ts";
    import Field from "$lib/struct/Field.ts";
    import FieldHtmlProperties from "$lib/struct/FieldHtmlProperties.ts";
    import CollectionField from "$lib/struct/CollectionField";
    import {onMount} from "svelte";
    import PaginatedTable from "$lib/admin/PaginatedTable/PaginatedTable.svelte";

    needsUser();

    let operations: Operation[] = [];

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
        operations = await getOperations();
    });
</script>

<h1>Operations</h1>

{#if operations.length}
    <div class="alert alert-info">
        Lines having a <span class="badge bg-warning">colored background</span> correspond to operations that have no tag associated.
        It is highly recommended to use the <i class="fa fa-plus"></i> icon to add new tags to them.
    </div>
{/if}

<PaginatedTable items_store={operationsStore} fields={fields} />
