import {
	CallbackStateProcessor,
	CallbackStateProvider,
	CrudDefinition,
	Edit,
	List,
	New,
	TextField,
	ToggleField,
	UrlAction,
	type RequestParameters,
	type CrudOperation,
	type StateProcessorInput
} from '@orbitale/svelte-admin';

import { createTagRule, getTagRuleById, getTagRules, updateTagRule } from '$lib/db/tag_rules';
import TagRule from '$lib/entities/TagRule';
import { goto } from '$app/navigation';
import { success } from '$lib/utils/message';
import type Tag from '$lib/entities/Tag';

const baseFields = [
	new TextField('tags', 'Tags'),
	new TextField('matching_pattern', 'Matching pattern'),
	new ToggleField('is_regex', 'Regex')
];

export default new CrudDefinition<TagRule>({
	name: 'tag-rules',
	defaultOperationName: 'list',
	label: { plural: 'TagRules', singular: 'TagRule' },
	// minStateLoadingTimeMs: 0,

	operations: [
		new List(
			[new TextField('id', 'ID'), ...baseFields],
			[new UrlAction('Edit', '/crud/tag-rules/edit')],
			{
				globalActions: [new UrlAction('New', '/crud/tag-rules/new')]
			}
		),
		new New(baseFields),
		new Edit(baseFields)
	],

	stateProvider: new CallbackStateProvider<TagRule>(
		async (operation: CrudOperation, requestParameters: RequestParameters) => {
			if (typeof window === 'undefined') {
				// SSR, can't call Tauri API then.
				return Promise.resolve([]);
			}

			if (operation.name === 'list') {
				return getTagRules();
			}

			if (operation.name === 'view' || operation.name === 'edit') {
				return getTagRuleById(String(requestParameters.id));
			}

			return Promise.resolve(null);
		}
	),

	stateProcessor: new CallbackStateProcessor<TagRule>(
		async (
			data: StateProcessorInput<TagRule>,
			operation: CrudOperation,
			requestParameters: RequestParameters
		) => {
			if (operation.name === 'new' || operation.name === 'edit') {
				if (!data) {
					throw new Error('Cannot create new object: empty data.');
				}
				if (Array.isArray(data)) {
					throw new Error('Cannot update data as array for this action.');
				}

				data.id = Number(requestParameters.id || 0);
				// TODO FIXME : remove this and use proper entity injection!
				data.is_regex = Boolean(data.is_regex);
				if (!Array.isArray(data.tags)) {
					// @ts-ignore
					data.tags = String(data.tags)
						.replace(/[^0-9,]/, '')
						.split(',')
						.map((i: string) => Number(i));
				}
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
		}
	)
});
