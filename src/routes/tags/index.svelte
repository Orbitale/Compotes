<script lang="ts">
    import {getTags, tagsStore} from "$lib/db/tags.ts";
    import Tag from "$lib/entities/Tag.ts";
    import PaginatedTable from "$lib/admin/components/PaginatedTable/PaginatedTable.svelte";
    import ActionParams from "$lib/admin/ActionParams";
    import Field from "$lib/admin/Field";
    import UrlAction from "$lib/admin/UrlAction";
    import PageHooks from "$lib/admin/PageHooks";

    let tags: Tag[] = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('name', 'Date'),
    ];

    let actions = [
        new UrlAction('Edit', '/tags/edit/:id', ActionParams.id()),
    ];

    const pageHooks = new PageHooks(getTags);
</script>

<h1>Tags</h1>

<PaginatedTable items_store={tagsStore} fields={fields} actions={actions} pageHooks={pageHooks} />
