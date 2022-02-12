<script lang="ts">

    import Tag from "$lib/entities/Tag";
    import PaginatedTable from "$lib/components/PaginatedTable/PaginatedTable.svelte";
    import EmptyCollection from "$lib/components/PaginatedTable/EmptyCollection.svelte";
    import Field from "$lib/struct/Field";
    import FieldHtmlProperties from "$lib/struct/FieldHtmlProperties";
    import ItemAction from "$lib/struct/ItemAction";
    import ActionParams from "$lib/struct/ActionParams";

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
        new ItemAction('Edit', '/tags/edit/:id', ActionParams.id()),
    ];
</script>

<h1>Triage</h1>

{#if triaged_operations.length}
    <PaginatedTable items={triaged_operations} fields={fields} actions={actions} />
{:else}
    <EmptyCollection {fields} />
{/if}
