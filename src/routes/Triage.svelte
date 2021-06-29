<script lang="ts">

    import Tag from "../entities/Tag";
    import PaginatedTable from "../components/PaginatedTable/PaginatedTable.svelte";
    import EmptyCollection from "../components/PaginatedTable/EmptyCollection.svelte";
    import Field from "../struct/Field";
    import FieldHtmlProperties from "../struct/FieldHtmlProperties";
    import ItemAction from "../struct/ItemAction";
    import ActionParams from "../struct/ActionParams";

    let triaged_operations: Tag[] = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('date', 'Date'),
        new Field('bank_account', 'Bank account', new Field('name')),
        new Field('op_type', 'Type'),
        new Field('details', 'Details'),
        new Field('amount_display', 'Amount', null, new FieldHtmlProperties('operation-amount')),
    ];

    let actions = [
        new ItemAction('Edit', '/tag/edit/:id', ActionParams.id()),
    ];
</script>

<h1>Triage</h1>

{#if triaged_operations.length}
    <PaginatedTable items={triaged_operations} fields={fields} actions={actions} />
{:else}
    <EmptyCollection {fields} />
{/if}
