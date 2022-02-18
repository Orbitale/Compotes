<script lang="ts">
    import Field from "$lib/struct/Field";
    import UrlAction from "$lib/struct/UrlAction";
    import PaginatedTable from "$lib/admin/PaginatedTable/PaginatedTable.svelte";
    import {Writable} from "svelte/types/runtime/store";

    export let items: object[] = [];
    export let items_store: Writable<any>;
    export let fields: Field[];
    export let actions: UrlAction[] = [];
</script>

<PaginatedTable items={items} items_store={items_store} fields={fields} actions={actions} />
