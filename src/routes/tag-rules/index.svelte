<script lang="ts">
    import {needsUser} from '$lib/auth/current_user.ts';
    import {getTagRules} from "$lib/db/tag_rules.ts";
    import PaginatedTable from "$lib/components/PaginatedTable/PaginatedTable.svelte";
    import Field from "$lib/struct/Field.ts";
    import EmptyCollection from "$lib/components/PaginatedTable/EmptyCollection.svelte";
    import UrlAction from "$lib/struct/UrlAction.ts";
    import ActionParams from "$lib/struct/ActionParams.ts";
    import {onMount} from "svelte";
    import CollectionField from "$lib/struct/CollectionField";

    needsUser();

    let tag_rules = [];

    let fields = [
        new Field('id', 'ID'),
        new CollectionField('tags', 'Tags', new Field('name')),
        new Field('is_regex', 'Regular expression'),
        new Field('matching_pattern', 'Matching pattern'),
    ];

    let actions = [
        new UrlAction('Edit', '/tag-rules/edit/:id', ActionParams.id()),
    ];

    onMount(async () => {
        tag_rules = await getTagRules();
    });
</script>

<style lang="scss">
  #new-button {
    float: right;
    margin-top: 8px;
  }
</style>

<a href="/tag-rules/new" class="btn btn-primary" id="new-button">New</a>

<h1>Tag rules</h1>

{#if tag_rules.length}
    <PaginatedTable items={tag_rules} fields={fields} actions={actions} />
{:else}
    <EmptyCollection {fields} />
{/if}
