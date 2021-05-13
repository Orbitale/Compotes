import type Tag from "./Tag";

export default class TagRule
{
    public readonly id!: number;
    public readonly tags!: Tag[];
    public readonly matching_pattern!: String;
    public readonly is_regex!: boolean;

    constructor(id: number, tags: Tag[], matching_pattern: String, is_regex: boolean) {
        this.id = id;
        this.tags = tags;
        this.matching_pattern = matching_pattern;
        this.is_regex = is_regex;
    }
}
