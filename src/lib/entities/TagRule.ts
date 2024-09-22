import type Tag from './Tag';
import type Entity from '$lib/struct/Entity';

export type PartialTagRule = {
	tags?: Array<Tag>;
	matching_pattern?: string;
	is_regex?: string | boolean;
};

export default class TagRule implements Entity {
	public id!: number;
	public tags!: Tag[];
	public matching_pattern!: string;
	public is_regex!: boolean;

	constructor(id: number, tags: Tag[], matching_pattern: string, is_regex: boolean) {
		this.id = id;
		this.tags = tags;
		this.matching_pattern = matching_pattern;
		this.is_regex = is_regex;
	}

	static empty(): TagRule {
		return new TagRule(0, [], '', false);
	}

	public clone(): TagRule {
		return new TagRule(this.id, this.tags, this.matching_pattern, this.is_regex);
	}

	public setId(id: number) {
		if (!id) {
			throw new Error('Cannot set an empty ID on an object.');
		}
		if (this.id > 0) {
			throw new Error('Cannot set an ID on an object that already has one.');
		}
		this.id = id;
	}

	public static fromJson(json: PartialTagRule): TagRule {
		if (
			!Array.isArray(json.tags) ||
			json.matching_pattern === '' ||
			json.matching_pattern === undefined ||
			json.is_regex === '' ||
			json.is_regex === undefined
		) {
			throw new Error('Invaild JSON to create a TagRule object: ' + JSON.stringify(json));
		}
		return new TagRule(0, json.tags, json.matching_pattern, !!json.is_regex);
	}

	public serialize(): string {
		return JSON.stringify(
			{
				id: this.id,
				tags_ids: this.tagsIds().join(','),
				matching_pattern: this.matching_pattern,
				is_regex: this.is_regex
			},
			null,
			4
		);
	}

	private tagsIds(): Array<number> {
		return this.tags.map((tag: Tag) => tag.id);
	}
}
