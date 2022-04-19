// @ts-ignore
import TagRule from '$lib/entities/TagRule';
import { getTagById } from './tags';
import api_call from '$lib/utils/api_call';
import { writable } from 'svelte/store';
import type { Writable } from 'svelte/store';

export const tagRulesStore: Writable<TagRule[]> = writable();

let tag_rules: TagRule[] = [];

export default class DeserializedTagRule {
	public readonly id!: number;
	public readonly tags_ids!: Array<number>;
	public readonly matching_pattern!: string;
	public readonly is_regex!: boolean;
}

export async function getTagRules(): Promise<Array<TagRule>> {
	if (!tag_rules.length) {
		let res: string = await api_call('tag_rules_get');
		const deserialized_tag_rules: Array<DeserializedTagRule> = JSON.parse(res);

		tag_rules = await Promise.all(
			deserialized_tag_rules.map(async (deserialized_tag_rule: DeserializedTagRule) => {
				return new TagRule(
					deserialized_tag_rule.id,
					await Promise.all(
						deserialized_tag_rule.tags_ids.map(async (id: number) => {
							return await getTagById(id);
						})
					),
					deserialized_tag_rule.matching_pattern,
					deserialized_tag_rule.is_regex
				);
			})
		);

		tagRulesStore.set(tag_rules);
	}

	return Promise.resolve(tag_rules);
}

export async function getTagRuleById(id: string): Promise<TagRule | null> {
	if (!tag_rules.length) {
		await getTagRules();
	}

	for (const tag_rule of tag_rules) {
		if (tag_rule.id.toString() === id.toString()) {
			return tag_rule;
		}
	}

	return null;
}

export async function updateTagRule(tag_rule: TagRule): Promise<void> {
	await api_call('tag_rule_update', { tagRule: tag_rule.serialize() });
}

export async function createTagRule(tag_rule: TagRule): Promise<void> {
	let id = await api_call('tag_rule_create', { tagRule: tag_rule.serialize() });

	if (isNaN(+id)) {
		throw new Error('Internal error: API returned a non-number ID.');
	}

	tag_rule.setId(+id);
}
