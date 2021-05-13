<script lang="ts">
    import AssociatedItemDisplay from "./AssociatedItemDisplay.svelte";
    import AssociatedItem from "../../struct/AssociatedItem.ts";
    import AssociatedCollection from "../../struct/AssociatedCollection.ts";
    import AssociatedItemCollectionDisplay from "./AssociatedItemCollectionDisplay.svelte";

    export let item: object;
    export let fields: Array<object>;
</script>

<tr>
    {#each fields as field}
        <td>

            {#if item[field.name] instanceof AssociatedCollection}
                <AssociatedItemCollectionDisplay collection={item[field.name]} />
            {:else if item[field.name] instanceof AssociatedItem}
                <AssociatedItemDisplay item={item[field.name]} />
            {:else if item[field.name] === true || item[field.name] === false}
                {#if item[field.name] === true}
                    <span class="badge rounded-pill bg-success">&check;</span>
                {:else}
                    <span class="badge rounded-pill bg-danger">&times;</span>
                {/if}
            {:else}
                {item[field.name]}
            {/if}
        </td>
    {/each}
</tr>
