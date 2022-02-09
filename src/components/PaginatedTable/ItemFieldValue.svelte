<script lang="ts">
    import Field from "../../struct/Field.ts";

    export let field: Field;
    export let item: object;

    let field_value = field.displayFromItem(item);
</script>

{#if field_value === null}
    <span class="badge bg-dark">Empty</span>

{:else if field_value === ''}
    <span class="badge bg-dark">Empty string</span>

{:else if field_value === undefined}
    <span class="badge bg-dark">Undefined</span>

{:else if field_value === true || field_value === false}
    {#if field_value === true}
        <span class="badge rounded-pill bg-Success">&check;</span>
    {:else}
        <span class="badge rounded-pill bg-danger">&times;</span>
    {/if}

{:else if typeof field_value === 'object'}
    <pre>{JSON.stringify(field_value)}</pre>

{:else}
    {field_value}
{/if}
