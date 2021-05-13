import AssociatedItem from "./AssociatedItem.ts";

export default class AssociatedCollection {
    public readonly items: AssociatedItem[];

    constructor(items: AssociatedItem[]) {
        this.items = items;
    }
}