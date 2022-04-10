import type Entity from '$lib/struct/Entity';

export default class Tag implements Entity {
	public id!: number;
	public name!: string;

	constructor(id: number, name: string) {
		this.id = id;
		this.name = name;
	}

	static empty(): Tag {
		return new Tag(0, '');
	}

	setId(id: number): void {
		this.id = id;
	}

	clone(): Tag {
		return new Tag(+this.id, '' + this.name);
	}

	serialize(): string {
		return JSON.stringify({
			id: this.id,
			name: this.name
		});
	}

	mergeWith(tag: Tag): void {
		if (tag.id.toString() !== this.id.toString()) {
			throw new Error('It is not possible to merge two tags that do not share the same ID.');
		}
		this.name = tag.name;
	}
}
