
export default class Tag
{
    public readonly id!: number;
    public name!: String;

    constructor(id: number, name: String) {
        this.id = id;
        this.name = name;
    }

    clone(): Tag {
        return new Tag(+this.id, ''+this.name);
    }

    serialize(): string {
        return JSON.stringify({
            id: this.id,
            name: this.name,
        });
    }

    mergeWith(tag: Tag): void {
        if (tag.id.toString() !== this.id.toString()) {
            throw new Error('It is not possible to merge two tags that do not share the same ID.');
        }
        this.name = tag.name;
    }
}