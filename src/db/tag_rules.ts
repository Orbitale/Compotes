// @ts-ignore
import TagRule from '../entities/TagRule';
import {getTagById} from "./tags";
import api_fetch from "../utils/api_fetch";

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
                // @ts-ignore (promise vs tag array seems to be inconsistent somehow. TODO: fix it)
                tags,
                deserialized_tag_rule.matching_pattern,
                deserialized_tag_rule.is_regex
            );
        });
    }

    return Promise.resolve(tag_rules);
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

export async function saveTagRule(tag_rule: TagRule): Promise<void>
{
    await api_fetch("save_tag_rule", {tagRule: tag_rule.serialize()});

    const tag_rule_entity = await getTagRuleById(tag_rule.id.toString());

    if (!tag_rule_entity) throw new Error('Data corruption detected in tag rule.');

    tag_rule_entity.mergeWith(tag_rule);
}
