import type AssociatedField from "$lib/admin/AssociatedField";
import FieldHtmlProperties from "$lib/admin/FieldHtmlProperties";

export default class FieldOptions {
    public readonly associated_field: null | AssociatedField;
    public readonly field_html_properties: FieldHtmlProperties;

    constructor(
        associated_field: AssociatedField | null = null,
        field_html_properties: FieldHtmlProperties | null = null,
    ) {
        this.associated_field = associated_field;
        this.field_html_properties = field_html_properties || FieldHtmlProperties.defaults();
    }

    static defaults(): FieldOptions {
        return new FieldOptions();
    }

    static newWithAssociatedField(field: AssociatedField): FieldOptions {
        return new FieldOptions(field);
    }

    static newWithHtmlProperties(htmlProperties: FieldHtmlProperties): FieldOptions {
        return new FieldOptions(null, htmlProperties);
    }
}