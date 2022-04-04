export default class Collection<T> extends Array<T> {
    constructor(items: T[] = []) {
        super();
        this.addItems(items);
    }

    private addItems(items: T[] = []) {
        items.forEach(item => this.push(item));
    }
}
