<script lang="ts">
    import AssociatedCollection from "../../struct/AssociatedCollection.ts";
    import AssociatedItemDisplay from "./AssociatedItemDisplay.svelte";

    export let collection: AssociatedCollection;
</script>

{#each collection as item}
    <AssociatedItemDisplay item={item} />
{/each}
