import type AssociatedField from "./AssociatedField";
import type FieldHtmlProperties from "./FieldHtmlProperties";
import FieldOptions from "$lib/admin/FieldOptions";
import SortableField from "$lib/admin/SortableField";

export const Sortable = true;

export default class Field {
    public readonly name: string;
    public readonly text: string;
    public readonly field_html_properties: FieldHtmlProperties;
    public readonly sortable_field: SortableField | null;
    protected readonly _associated_field: null|AssociatedField;

    constructor(
        name: string,
        text: string = '',
        options: FieldOptions | null = null,
        sortable: boolean = false,
    ) {
        if (!options) {
            options = FieldOptions.defaults();
        }
        this.name = name;
        this.text = text === '' ? name : text;
        this.field_html_properties = options.field_html_properties;
        this._associated_field = options.associated_field;
        this.sortable_field = sortable ? new SortableField(name) : null;
    }

    public displayFromItem(item: object|any): any {
        let field: Field|AssociatedField;

        if (this._associated_field) {
            item = item[this.name];
            field = this._associated_field;
        } else {
            field = this;
        }

        return item[field.name];
    }
}