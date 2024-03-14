import {
    CallbackStateProcessor,
    CallbackStateProvider,
    CrudDefinition, Edit,
    List, New,
    TextField, ToggleField,
    UrlAction,
} from "@orbitale/svelte-admin";
import type {RequestParameters} from "@orbitale/svelte-admin/dist/request";
import type {CrudOperation} from "@orbitale/svelte-admin/dist/Crud/Operations";
import type {StateProcessorInput} from "@orbitale/svelte-admin/dist/State/Processor";

import {createTagRule, getTagRuleById, getTagRules, updateTagRule} from "$lib/db/tag_rules";
import TagRule from "$lib/entities/TagRule";
import {goto} from "$app/navigation";
import {success} from "$lib/utils/message";

const baseFields = [
    new TextField('tags', 'Tags'),
    new TextField('matching_pattern', 'Matching pattern'),
    new ToggleField('is_regex', 'Regex'),
];

export default new CrudDefinition<TagRule>({
    name: 'tag-rules',
    defaultOperationName: "list",
    label: {plural: "TagRules", singular: "TagRule"},
    // minStateLoadingTimeMs: 0,

    operations: [
        new List([new TextField('id', 'ID'), ...baseFields],
            [
                new UrlAction('Edit', '/crud/tag-rules/edit'),
            ], {
                globalActions: [
                    new UrlAction('New', '/crud/tag-rules/new'),
                ],
            }),
        new New(baseFields),
        new Edit(baseFields),
    ],

    stateProvider: new CallbackStateProvider<TagRule>(async (operation: CrudOperation, requestParameters: RequestParameters) => {
        if (typeof window === 'undefined') {
            // SSR, can't call Tauri API then.
            return Promise.resolve([]);
        }

        if (operation.name === 'list') {
            return getTagRules();
        }

        if (operation.name === 'view' || operation.name === 'edit') {
            return getTagRuleById(requestParameters.id);
        }

        return Promise.resolve(null);
    }),

    stateProcessor: new CallbackStateProcessor<TagRule>(async (data: StateProcessorInput<TagRule>, operation: CrudOperation, requestParameters: RequestParameters) => {
        if (operation.name === 'new' || operation.name === 'edit') {
            data.id = parseInt(requestParameters.id || 0, 10);
            // TODO FIXME : remove this and use proper entity injection!
            data.is_regex = !!data.is_regex;
            data.tags = data.tags.replace(/[^0-9,]/, '').split(',').map((i: string) => parseInt(i, 10));
            const tag_rule = TagRule.fromJson(data);

            if (operation.name === 'new') {
                await createTagRule(tag_rule);
            }
            if (operation.name === 'edit') {
                await updateTagRule(tag_rule);
            }

            success('Success!');
            await goto('/crud/tag-rules/list');
            return;
        }
    })
});
