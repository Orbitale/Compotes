import type AssociatedFieldToDisplay from "./AssociatedFieldToDisplay.ts";
import FieldHtmlProperties from "./FieldHtmlProperties.ts";

export default class FieldToDisplay {
    public readonly name: string;
    public readonly text: string;
    public readonly field_html_properties: null|FieldHtmlProperties;
    private readonly _associated_field: null|AssociatedFieldToDisplay;

    constructor(name: string, text: string = '', associated_field: null|AssociatedFieldToDisplay = null, field_html_properties: FieldHtmlProperties = FieldHtmlProperties.default()) {
        this.name = name;
        this.text = text === '' ? name : text;
        this.field_html_properties = field_html_properties;
        this._associated_field = associated_field;
    }

    public get associated_field(): null|AssociatedFieldToDisplay {
        return this._associated_field;
    }

    public get is_association() {
        return this._associated_field !== null;
    }
}
