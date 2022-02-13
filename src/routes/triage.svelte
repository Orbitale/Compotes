<script lang="ts">
    import PaginatedTable from "$lib/components/PaginatedTable/PaginatedTable.svelte";
    import EmptyCollection from "$lib/components/PaginatedTable/EmptyCollection.svelte";
    import Field from "$lib/struct/Field";
    import FieldHtmlProperties from "$lib/struct/FieldHtmlProperties";
    import ItemAction from "$lib/struct/ItemAction";
    import ActionParams from "$lib/struct/ActionParams";
    import {onMount} from "svelte";
    import {getTriageOperations} from "$lib/db/operations";
    import Operation from "$lib/entities/Operation";

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
        new ItemAction('Edit', '/tags/edit/:id', ActionParams.id()),
    ];

    onMount(async () => {
        triaged_operations = await getTriageOperations();
    });
</script>

<h1>Triage</h1>

{#if triaged_operations.length}
    <PaginatedTable items={triaged_operations} fields={fields} actions={actions} />
{:else}
    <EmptyCollection {fields} />
{/if}
