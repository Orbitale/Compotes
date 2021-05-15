import type AssociatedFieldToDisplay from "./AssociatedFieldToDisplay.ts";

export default class FieldToDisplay {
    public readonly name: string;
    public readonly text: string;
    private readonly _associated_field: null|AssociatedFieldToDisplay;

    constructor(name: string, text: string = '', associated_field: null|AssociatedFieldToDisplay = null) {
        this.name = name;
        this.text = text === '' ? name : text;
        this._associated_field = associated_field;
    }

    public get associated_field(): null|AssociatedFieldToDisplay {
        return this._associated_field;
    }

    public get is_association() {
        return this._associated_field !== null;
    }
}
