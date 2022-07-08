// @ts-ignore
import TagRule from '$lib/entities/TagRule';
import { getTagById } from './tags';
import api_call from '$lib/utils/api_call';
import { writable } from 'svelte/store';
import type { Writable } from 'svelte/store';
import type Tag from "$lib/entities/Tag";

export const tagRulesStore: Writable<TagRule[]> = writable();

let tag_rules_promise: Promise<TagRule[]>|null = null;

async function getTagRulesPromise(): Promise<TagRule[]> {
	if (!tag_rules_promise) {
		tag_rules_promise = getTagRules();
	}

	return tag_rules_promise;
}

export default class DeserializedTagRule {
	public readonly id!: number;
	public readonly tags_ids!: Array<number>;
	public readonly matching_pattern!: string;
	public readonly is_regex!: boolean;
}

export async function getTagRules(): Promise<Array<TagRule>> {
	if (tag_rules_promise) {
		return await tag_rules_promise;
	}

	let res: string = await api_call('tag_rules_get');
	const deserialized_tag_rules: Array<DeserializedTagRule> = JSON.parse(res);

	const tag_rules: Array<TagRule> = [];

	for (const deserialized_tag_rule of deserialized_tag_rules) {
		let tags_ids: Array<Tag> = [];

		for (const tag_id of deserialized_tag_rule.tags_ids) {
			if (isNaN(+tag_id)) {
				throw new Error(`Invalid tag ID ${tag_id} in tag rule ${deserialized_tag_rule.id}`);
			}
			const tag = await getTagById(+tag_id);

			if (null === tag) {
				console.error(`No tag for id ${tag_id} in tag rule ${deserialized_tag_rule.id}`);
				continue;
			}

			tags_ids.push(tag);
		}

		tag_rules.push(
			new TagRule(
				deserialized_tag_rule.id,
				tags_ids,
				deserialized_tag_rule.matching_pattern,
				deserialized_tag_rule.is_regex
			)
		)
	}

	tagRulesStore.set(tag_rules);

	return tag_rules;
}

export async function getTagRuleById(id: string): Promise<TagRule | null> {
	const tag_rules = await getTagRulesPromise();

	for (const tag_rule of tag_rules) {
		if (tag_rule.id.toString() === id.toString()) {
			return tag_rule;
		}
	}

	return null;
}

export async function updateTagRule(tag_rule: TagRule): Promise<void> {
	await api_call('tag_rule_update', { tagRule: tag_rule.serialize() });

	tag_rules_promise = null;
}

export async function createTagRule(tag_rule: TagRule): Promise<void> {
	let id = await api_call('tag_rule_create', { tagRule: tag_rule.serialize() });

	if (isNaN(+id)) {
		throw new Error('Internal error: API returned a non-number ID.');
	}

	tag_rule.setId(+id);

	tag_rules_promise = null;
}
