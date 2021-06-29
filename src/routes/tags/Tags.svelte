<script lang="ts">
    import {needsUser} from '../../auth/current_user.ts';
    import {getTags} from "../../db/tags.ts";
    import PaginatedTable from "../../components/PaginatedTable/PaginatedTable.svelte";
    import Tag from "../../entities/Tag.ts";
    import Field from "../../struct/Field.ts";
    import {onMount} from "svelte";
    import EmptyCollection from "../../components/PaginatedTable/EmptyCollection.svelte";
    import ItemAction from "../../struct/ItemAction.ts";
    import ActionParams from "../../struct/ActionParams.ts";

    needsUser();

    let tags: Tag[] = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('name', 'Date'),
    ];

    let actions = [
        new ItemAction('Edit', '/tag/edit/:id', ActionParams.id()),
    ];

    onMount(async () => tags = await getTags())
</script>

<h1>Tags</h1>

{#if tags.length}
    <PaginatedTable items={tags} fields={fields} actions={actions} />
{:else}
    <EmptyCollection {fields} />
{/if}
