<script lang="ts">
    import AssociatedItem from "../../struct/AssociatedItem.ts";
    import FieldToDisplay from "../../struct/FieldToDisplay.ts";

    export let item: AssociatedItem;
    export let fields: FieldToDisplay[] = item.fields;
</script>

<style lang="scss">
    ul, li {
        list-style-type: none;
        list-style-image: none;
        margin: 0;
        padding: 0;
    }
</style>

<ul>
    {#each fields as field}
        <li>{item.item[field.name]}</li>
    {/each}
</ul>
