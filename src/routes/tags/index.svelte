<script lang="ts">
    import {needsUser} from '$lib/auth/current_user.ts';
    import {getTags} from "$lib/db/tags.ts";
    import Tag from "$lib/entities/Tag.ts";
    import Field from "$lib/struct/Field.ts";
    import {onMount} from "svelte";
    import UrlAction from "$lib/struct/UrlAction.ts";
    import ActionParams from "$lib/struct/ActionParams.ts";
    import PaginatedTable from "$lib/admin/PaginatedTable/PaginatedTable.svelte";

    needsUser();

    let tags: Tag[] = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('name', 'Date'),
    ];

    let actions = [
        new UrlAction('Edit', '/tags/edit/:id', ActionParams.id()),
    ];

    onMount(async () => tags = await getTags())
</script>

<h1>Tags</h1>

<PaginatedTable items={tags} fields={fields} actions={actions} />
