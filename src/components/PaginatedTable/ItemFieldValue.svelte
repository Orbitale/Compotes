<script lang="ts">
    import AssociatedItemDisplay from "./AssociatedItemDisplay.svelte";
    import AssociatedItem from "../../struct/AssociatedItem.ts";
    import AssociatedCollection from "../../struct/AssociatedCollection.ts";
    import AssociatedItemCollectionDisplay from "./AssociatedItemCollectionDisplay.svelte";
    import FieldToDisplay from "../../struct/FieldToDisplay.ts";

    export let field: FieldToDisplay;
    export let item: object;

    let field_value;

    if (field.is_association) {
        item = item[field.name];
        field = field.associated_field;
    }

    field_value = item[field.name];
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
