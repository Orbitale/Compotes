import type Field from "./Field.ts";

export default class AssociatedItem {
    public readonly item: object;
    public readonly fields: Field[];

    constructor(item: object, fields: Field[]) {
        this.item = item;
        this.fields = fields;
    }
}