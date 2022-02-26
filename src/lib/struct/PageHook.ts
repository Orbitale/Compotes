
export default class PageHook {
    private readonly _callback: Function;

    constructor(cb: Function) {
        this._callback = cb;
    }

    public call(page: number) {
        this._callback(page);
    }
};
