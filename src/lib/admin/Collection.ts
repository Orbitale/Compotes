import type Field from "./Field";

export default class Collection<T> extends Array<T> {
    private _field: Field;

    constructor(field: Field, items?: T[]) {
        super();
        items && this.addItems(items);
        this._field = field;
    }

    private addItems(items: T[]) {
        if (!items.length) {
            console.warn(`Tried to add item to collection, got ${items} instead.`);
            return;
        }
        items.forEach(item => this.push(item));
    }
}