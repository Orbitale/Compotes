<script lang="ts">
    import {needsUser} from '../../auth/current_user.ts';
    import {getTagRules} from "../../db/tag_rules.ts";
    import PaginatedTable from "../../components/PaginatedTable/PaginatedTable.svelte";
    import Tag from "../../entities/Tag.ts";
    import TagRule from "../../entities/TagRule.ts";
    import AssociatedItem from "../../struct/AssociatedItem.ts";
    import Field from "../../struct/Field.ts";
    import AssociatedCollection from "../../struct/AssociatedCollection.ts";
    import EmptyCollection from "../../components/PaginatedTable/EmptyCollection.svelte";
    import ItemAction from "../../struct/ItemAction.ts";
    import ActionParams from "../../struct/ActionParams.ts";

    needsUser();

    let tag_rules = [];
    let waiting_tag_rules = [];

    let fields = [
        new Field('id', 'ID'),
        new Field('tags', 'Tags'),
        new Field('matching_pattern', 'Matching pattern'),
    ];

    let actions = [
        new ItemAction('Edit', '/tag-rule/edit/:id', ActionParams.id()),
    ];

    getTagRules()
        .then((awaited_tag_rules: TagRule[]) => {
            let number_of_tag_rules = awaited_tag_rules.length;
            let current_tag_rule = 0;

            function add_tag_rule(tag_rule: TagRule) {
                waiting_tag_rules.push(tag_rule);
                current_tag_rule++;
                if (current_tag_rule === number_of_tag_rules) {
                    update();
                }
            }

            awaited_tag_rules.forEach((tag_rule: TagRule) => {
                let tags: AssociatedItem<Tag>[] = [];
                let awaited_tags: Array<Promise<Tag>> = tag_rule.tags;
                let number_of_tags = awaited_tags.length;
                let current_tag = 0;
                if (awaited_tags instanceof AssociatedCollection) {
                    add_tag_rule(tag_rule);
                    // This function passed already.
                    return;
                }
                awaited_tags.forEach((promised_tag: Promise<Tag>) => {
                    promised_tag.then((tag: Tag) => {
                        tags.push(new AssociatedItem<Tag>(tag, [
                            new Field('name', 'Name'),
                        ]));
                        current_tag++;
                        if (current_tag === number_of_tags) {
                            tag_rule.tags = new AssociatedCollection(tags);
                            add_tag_rule(tag_rule);
                        }
                    });
                });
            });
        })
    ;

    function update() {
        tag_rules = waiting_tag_rules;
    }
</script>

{#if tag_rules.length}
    <PaginatedTable items={tag_rules} fields={fields} actions={actions} />
{:else}
    <EmptyCollection />
{/if}
