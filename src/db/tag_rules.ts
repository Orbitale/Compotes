// @ts-ignore
import TagRule from '../entities/TagRule.ts';
import {getTagById, getTags} from "./tags.ts";
import api_fetch from "../utils/api_fetch.ts";

export default class DeserializedTagRule
{
    public readonly id!: number;
    public readonly tags_ids!: Array<number>;
    public readonly matching_pattern!: String;
    public readonly is_regex!: boolean;
}

let tag_rules: TagRule[] = [];

export async function getTagRules(): Promise<Array<TagRule>>
{
    if (!tag_rules.length) {
        let res: string = await api_fetch("get_tag_rules");
        const deserialized_tag_rules: Array<DeserializedTagRule> = JSON.parse(res);

        tag_rules = await deserialized_tag_rules.map((deserialized_tag_rule: DeserializedTagRule) => {
            let tags = deserialized_tag_rule.tags_ids.map(async (id: number) => {
                return await getTagById(id.toString());
            });

            return new TagRule(
                deserialized_tag_rule.id,
                tags,
                deserialized_tag_rule.matching_pattern,
                deserialized_tag_rule.is_regex
            );
        });
    }

    return tag_rules;
}

export async function getTagRuleById(id: string): Promise<TagRule | null>
{
    if (!tag_rules.length) {
        await getTagRules();
    }

    for (const tag_rule of tag_rules) {
        if (tag_rule.id.toString() === id) {
            return tag_rule;
        }
    }

    return null;
}
