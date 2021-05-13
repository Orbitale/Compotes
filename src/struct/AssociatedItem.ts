import type FieldToDisplay from "./FieldToDisplay.ts";

export default class AssociatedItem {
    public readonly item: object;
    public readonly fields: FieldToDisplay[];

    constructor(item: object, fields: FieldToDisplay[]) {
        this.item = item;
        this.fields = fields;
    }
}