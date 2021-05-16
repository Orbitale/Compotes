<script lang="ts">
    import ItemFieldValue from "./ItemFieldValue.svelte";
    import FieldToDisplay from "../../struct/FieldToDisplay.ts";

    export let item: object;
    export let fields: Array<FieldToDisplay>;
</script>

<tr>
    {#each fields as field}
        <td
            class={field.field_html_properties.html_class}
        >
            <ItemFieldValue item={item} field={field} />
        </td>
    {/each}
</tr>
