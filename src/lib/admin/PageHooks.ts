export default class PageHooks {
    private readonly _items_callback: Function;
    private readonly _number_of_pages_callback: Function;

    constructor(items_callback: Function, number_of_pages_callback: Function = null) {
        this._items_callback = items_callback;
        this._number_of_pages_callback = number_of_pages_callback || null;
    }

    get hasCountCallback(): boolean {
        return this._number_of_pages_callback !== null;
    }

    public callForItems(page: number): void {
        this._items_callback(page);
    }

    public getCountCallback(): Function {
        return this._number_of_pages_callback();
    }
};