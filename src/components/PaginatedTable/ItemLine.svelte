<script lang="ts">
    import ItemFieldValue from "./ItemFieldValue.svelte";
    import Field from "../../struct/Field.ts";
    import ItemAction from "../../struct/ItemAction.ts";

    export let item: object;
    export let fields: Array<Field>;
    export let actions: ItemAction[] = [];
</script>

<style lang="scss">
    .action-link {
        display: block;
    }
</style>

<tr>
    {#each fields as field}
        <td
            class={field.field_html_properties.html_class}
        >
            <ItemFieldValue item={item} field={field} />
        </td>
    {/each}
    {#if actions.length}
        <td>
            {#each actions as action}
                <a href="{action.url(item)}" class="action-link">
                    {action.name}
                </a>
            {/each}
        </td>
    {/if}
</tr>
