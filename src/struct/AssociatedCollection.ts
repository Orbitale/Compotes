import type AssociatedItem from "./AssociatedItem.ts";

class Collection<T> extends Array<T> {
    constructor(items?: T[]) {
        super();
        items && this.addItems(items);
    }

    private addItems(items: T[]) {
        if (!items.length) {
            console.warn(`Tried to add item to collection, got ${items} instead.`);
            return;
        }
        items.forEach(item => this.push(item));
    }
}

export default class AssociatedCollection extends Collection<AssociatedItem> {
}
