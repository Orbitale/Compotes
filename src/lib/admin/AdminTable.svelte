<script lang="ts">
    import Field from "$lib/struct/Field";
    import UrlAction from "$lib/struct/UrlAction";
    import PaginatedTable from "$lib/admin/PaginatedTable/PaginatedTable.svelte";
    import EmptyCollection from "$lib/admin/PaginatedTable/EmptyCollection.svelte";

    export let items: object[];
    export let fields: Field[];
    export let actions: UrlAction[] = [];
</script>

{#if items.length}
    <PaginatedTable bind:items={items} fields={fields} actions={actions} />
{:else}
    <EmptyCollection {fields} />
{/if}
