import type ItemAction from "./ItemAction";

export default abstract class DefaultAction implements ItemAction {
    protected readonly _name: string;

    protected constructor(name: string) {
        this._name = name;
    }

    get name(): string {
        return this._name;
    }
}