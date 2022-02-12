<script lang="ts">
    import {needsUser} from '$lib/auth/current_user.ts';
    import {getTags} from "$lib/db/tags.ts";
    import PaginatedTable from "$lib/components/PaginatedTable/PaginatedTable.svelte";
    import Tag from "$lib/entities/Tag.ts";
    import Field from "$lib/struct/Field.ts";
    import {onMount} from "svelte";
    import EmptyCollection from "$lib/components/PaginatedTable/EmptyCollection.svelte";
    import ItemAction from "$lib/struct/ItemAction.ts";
    import ActionParams from "$lib/struct/ActionParams.ts";

    needsUser();

    let tags: Tag[] = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('name', 'Date'),
    ];

    let actions = [
        new ItemAction('Edit', '/tags/edit/:id', ActionParams.id()),
    ];

    onMount(async () => tags = await getTags())
</script>

<h1>Tags</h1>

{#if tags.length}
    <PaginatedTable items={tags} fields={fields} actions={actions} />
{:else}
    <EmptyCollection {fields} />
{/if}
