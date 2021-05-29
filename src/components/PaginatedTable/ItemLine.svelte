<script lang="ts">
    import ItemFieldValue from "./ItemFieldValue.svelte";
    import Field from "../../struct/Field.ts";
    import ItemAction from "../../struct/ItemAction.ts";

    export let item: object;
    export let fields: Array<Field>;
    export let actions: ItemAction[] = [];
</script>

<style lang="scss">
    td {
        vertical-align: middle;
    }
    .item-actions {
        text-align: center;
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
        <td class="item-actions">
            {#each actions as action}
                <a href="{action.url(item)}" class="btn btn-sm btn-primary">
                    {action.name}
                </a>
            {/each}
        </td>
    {/if}
</tr>
