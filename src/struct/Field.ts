import type AssociatedField from "./AssociatedField.ts";
import FieldHtmlProperties from "./FieldHtmlProperties.ts";

export default class Field {
    public readonly name: string;
    public readonly text: string;
    public readonly field_html_properties: FieldHtmlProperties;
    private readonly _associated_field: null|AssociatedField;

    constructor(
        name: string,
        text: string = '',
        associated_field: null|AssociatedField = null,
        field_html_properties: FieldHtmlProperties = FieldHtmlProperties.defaults()
    ) {
        this.name = name;
        this.text = text === '' ? name : text;
        if (!field_html_properties) {
            field_html_properties = FieldHtmlProperties.defaults();
        }
        this.field_html_properties = field_html_properties;
        this._associated_field = associated_field;
    }

    public get associated_field(): null|AssociatedField {
        return this._associated_field;
    }

    public get is_association() {
        return this._associated_field !== null;
    }
}
