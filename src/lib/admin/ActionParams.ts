import Field from "./Field";

export default class ActionParams {
    private _params: Array<Field> = [];

    get params(): Array<Field> {
        return this._params;
    }

    public static id(): ActionParams {
        return (new ActionParams()).and('id');
    }

    public static with(field: string | Field): ActionParams {
        return (new ActionParams()).and(field);
    }

    public and(field: string | Field): ActionParams {
        if (field instanceof Field) {
            this._params.push(field);
        } else {
            this._params.push(new Field(field));
        }

        return this;
    }
}