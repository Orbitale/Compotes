<script lang="ts">
    import Field from "$lib/admin/Field";
    import {OrderBy} from "$lib/admin/OrderBy";
    import {writable} from "svelte/store";

    export let field: Field;
    export let sort_callback: Function;

    let order_state = writable<OrderBy>(OrderBy.ASC);

    async function sortField(field: Field) {
        if (!field.sortable_field) {
            return;
        }

        let order = field.sortable_field.order_by;

        field.sortable_field.order_by = order === OrderBy.ASC ? OrderBy.DESC : OrderBy.ASC;
        order_state.set(field.sortable_field.order_by);

        if (sort_callback) {
            sort_callback(field);
        }
    }
</script>

<th
    class:sortable={field.sortable_field !== null}
    class:sort_asc={$order_state === OrderBy.ASC}
    class:sort_desc={$order_state === OrderBy.DESC}
    on:click={() => sortField(field)}
>
    {field.text}
</th>

<style lang="scss">
  th {
    vertical-align: middle;
    white-space: nowrap;
    &.sortable {
      cursor: pointer;
      &::before {
        display: inline-block;
      }
      &.sort_asc::before {
        content: "🔼 ";
      }
      &.sort_desc::before {
        content: "🔽 ";
      }
      &:hover {
        color: #333;
        background: #eee;
      }
    }
  }
</style>
