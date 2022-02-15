import DefaultAction from "$lib/struct/DefaultAction";

export default class CallbackAction extends DefaultAction
{
    private readonly _callback: Function;

    constructor(name: string, callback: Function) {
        super(name);
        this._callback = callback;
    }

    public call(item: object): string {
        return this._callback.call(null, [item]);
    }
};
