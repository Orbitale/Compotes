import {
    CallbackStateProcessor,
    CallbackStateProvider,
    CrudDefinition, Edit,
    List, New,
    TextField,
    UrlAction,
} from "@orbitale/svelte-admin";
import type {RequestParameters} from "@orbitale/svelte-admin/dist/request";
import type {CrudOperation} from "@orbitale/svelte-admin/dist/Crud/Operations";
import type {StateProcessorInput} from "@orbitale/svelte-admin/dist/State/Processor";

import {createTag, getTagById, getTags, updateTag} from "$lib/db/tags";
import Tag from "$lib/entities/Tag";
import {goto} from "$app/navigation";
import {success} from "$lib/utils/message";

const baseFields = [
    new TextField('name', 'Name'),
];

export default new CrudDefinition<Tag>({
    name: 'tags',
    defaultOperationName: "list",
    label: {plural: "Tags", singular: "Tag"},
    // minStateLoadingTimeMs: 0,

    operations: [
        new List([new TextField('id', 'ID'), ...baseFields],
            [
                new UrlAction('Edit', '/crud/tags/edit'),
            ], {
                globalActions: [
                    new UrlAction('New', '/crud/tags/new'),
                ],
            }),
        new New(baseFields),
        new Edit(baseFields),
    ],

    stateProvider: new CallbackStateProvider<Tag>(async (operation: CrudOperation, requestParameters: RequestParameters) => {
        if (typeof window === 'undefined') {
            // SSR, can't call Tauri API then.
            return Promise.resolve([]);
        }

        if (operation.name === 'list') {
            return getTags();
        }

        if (operation.name === 'view' || operation.name === 'edit') {
            return getTagById(requestParameters.id);
        }

        return Promise.resolve(null);
    }),

    stateProcessor: new CallbackStateProcessor<Tag>(async (data: StateProcessorInput<Tag>, operation: CrudOperation, requestParameters: RequestParameters) => {
        if (operation.name === 'new' || operation.name === 'edit') {
            data.id = parseInt(requestParameters.id || 0, 10);
            // TODO FIXME : remove this and use proper entity injection!
            const tag_rule = Tag.fromJson(data);

            if (operation.name === 'new') {
                await createTag(tag_rule);
            }
            if (operation.name === 'edit') {
                await updateTag(tag_rule);
            }

            success('Success!');
            await goto('/crud/tags/list');
            return;
        }
    })
});
