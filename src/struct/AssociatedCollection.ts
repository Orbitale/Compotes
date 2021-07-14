import type AssociatedItem from "./AssociatedItem";

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

export default class AssociatedCollection<T> extends Collection<AssociatedItem<T>> {
}
