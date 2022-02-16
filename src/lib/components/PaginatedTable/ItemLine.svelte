<script lang="ts">
    import ItemFieldValue from "./ItemFieldValue.svelte";
    import Field from "$lib/struct/Field.ts";
    import UrlAction from "$lib/struct/UrlAction.ts";
    import CallbackAction from "$lib/struct/CallbackAction.ts";

    export let item: object;
    export let fields: Array<Field>;
    export let actions: UrlAction[] = [];
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
                {#if action instanceof UrlAction}
                    <a href="{action.url(item)}" class="btn btn-sm btn-primary">
                        {action.name}
                    </a>
                {:else if action instanceof CallbackAction}
                    <button on:click={async () => await action.call(item)} class="btn btn-sm btn-primary">
                        {action.name}
                    </button>
                {:else}
                    <span class="badge bg-danger">Unsupported action</span>
                {/if}
            {/each}
        </td>
    {/if}
</tr>
