<script lang="ts">
    import {needsUser} from '../auth/current_user.ts';
    import {getTags} from "../db/tags.ts";
    import PaginatedTable from "../components/PaginatedTable/PaginatedTable.svelte";
    import Tag from "../entities/Tag.ts";
    import FieldToDisplay from "../struct/FieldToDisplay.ts";
    import {onMount} from "svelte";
    import EmptyCollection from "../components/PaginatedTable/EmptyCollection.svelte";
    import ItemAction from "../struct/ItemAction.ts";

    needsUser();

    let tags: Tag[] = [];

    let fields = [
        new FieldToDisplay('id', 'ID'),
        new FieldToDisplay('name', 'Date'),
    ];

    let actions = [
        new ItemAction(),
    ];

    onMount(async () => tags = await getTags())
</script>

{#if tags.length}
    <PaginatedTable items={tags} fields={fields} actions={actions} />
{:else}
    <EmptyCollection />
{/if}
