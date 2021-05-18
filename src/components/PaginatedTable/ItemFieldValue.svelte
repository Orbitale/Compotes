<script lang="ts">
    import AssociatedItemDisplay from "./AssociatedItemDisplay.svelte";
    import AssociatedItem from "../../struct/AssociatedItem.ts";
    import AssociatedCollection from "../../struct/AssociatedCollection.ts";
    import AssociatedItemCollectionDisplay from "./AssociatedItemCollectionDisplay.svelte";
    import Field from "../../struct/Field.ts";

    export let field: Field;
    export let item: object;

    let field_value = field.get_from_item(item);
</script>

{#if field_value instanceof AssociatedCollection}
    <AssociatedItemCollectionDisplay collection={field_value} />

{:else if field_value instanceof AssociatedItem}
    <AssociatedItemDisplay item={field_value} />

{:else if field_value === undefined}
    <span class="badge bg-dark">Undefined</span>

{:else if field_value === true || field_value === false}
    {#if field_value === true}
        <span class="badge rounded-pill bg-success">&check;</span>
    {:else}
        <span class="badge rounded-pill bg-danger">&times;</span>
    {/if}

{:else}
    {field_value}
{/if}
