import type AssociatedField from './AssociatedField';
import FieldHtmlProperties from './FieldHtmlProperties';

export default class FieldOptions {
	public readonly associated_field: null | AssociatedField;
	public readonly field_html_properties: FieldHtmlProperties;
	public readonly sortable_property_name: string | null;

	constructor(
		associated_field: AssociatedField | null = null,
		field_html_properties: FieldHtmlProperties | null = null,
		sortable_property_name: string | null = null
	) {
		this.associated_field = associated_field;
		this.field_html_properties = field_html_properties || FieldHtmlProperties.defaults();
		this.sortable_property_name = sortable_property_name;
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

	static newWithSortName(sortable_property_name: string): FieldOptions {
		return new FieldOptions(null, null, sortable_property_name);
	}
}
