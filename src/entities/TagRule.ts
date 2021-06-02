import type Tag from "./Tag";

export default class TagRule
{
    public readonly id!: number;
    public tags!: Tag[];
    public matching_pattern!: String;
    public is_regex!: boolean;

    constructor(id: number, tags: Tag[], matching_pattern: String, is_regex: boolean) {
        this.id = id;
        this.tags = tags;
        this.matching_pattern = matching_pattern;
        this.is_regex = is_regex;
    }

    clone(): TagRule {
        return new TagRule(this.id, this.tags, this.matching_pattern, this.is_regex);
    }

    serialize(): string {
        console.info('serializing', this);
        return JSON.stringify({
            id: this.id,
            tags_ids: this.tagsIds().join(','),
            matching_pattern: this.matching_pattern,
            is_regex: this.is_regex
        });
    }

    mergeWith(tag_rule: TagRule): void {
        if (tag_rule.id.toString() !== this.id.toString()) {
            throw new Error('It is not possible to merge two tag rules that do not share the same ID.');
        }
        this.tags = tag_rule.tags;
        this.matching_pattern = tag_rule.matching_pattern;
        this.is_regex = tag_rule.is_regex;
    }

    private tagsIds(): Array<number> {
        return this.tags.map((tag: Tag) => tag.id);
    }
}
