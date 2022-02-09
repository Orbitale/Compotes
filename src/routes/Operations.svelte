<script lang="ts">
    import {needsUser} from '../auth/current_user.ts';
    import {getOperations} from "../db/operations.ts";
    import PaginatedTable from "../components/PaginatedTable/PaginatedTable.svelte";
    import Operation from "../entities/Operation.ts";
    import Field from "../struct/Field.ts";
    import EmptyCollection from "../components/PaginatedTable/EmptyCollection.svelte";
    import FieldHtmlProperties from "../struct/FieldHtmlProperties.ts";
    import CollectionField from "../struct/CollectionField";
    import {onMount} from "svelte";

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
        new CollectionField('tags', new Field('name')),
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

    <PaginatedTable items={operations} fields={fields} />
{:else}
    <EmptyCollection {fields} />
{/if}
