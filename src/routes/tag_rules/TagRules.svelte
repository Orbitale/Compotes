<script lang="ts">
    import {needsUser} from '../../auth/current_user.ts';
    import {getTagRules} from "../../db/tag_rules.ts";
    import PaginatedTable from "../../components/PaginatedTable/PaginatedTable.svelte";
    import Field from "../../struct/Field.ts";
    import EmptyCollection from "../../components/PaginatedTable/EmptyCollection.svelte";
    import ItemAction from "../../struct/ItemAction.ts";
    import ActionParams from "../../struct/ActionParams.ts";
    import {onMount} from "svelte";
    import CollectionField from "../../struct/CollectionField";

    needsUser();

    let tag_rules = [];

    let fields = [
        new Field('id', 'ID'),
        new CollectionField('tags', new Field('name')),
        //new AssociatedCollection('tags', new AssociatedField('name', 'Tag')),
        new Field('matching_pattern', 'Matching pattern'),
    ];

    let actions = [
        new ItemAction('Edit', '/tag-rule/edit/:id', ActionParams.id()),
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

<a href="#/tag-rule/new" class="btn btn-primary" id="new-button">New</a>

<h1>Tag rules</h1>

{#if tag_rules.length}
    <PaginatedTable items={tag_rules} fields={fields} actions={actions} />
{:else}
    <EmptyCollection {fields} />
{/if}
