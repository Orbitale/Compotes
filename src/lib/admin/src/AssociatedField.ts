import Field from './Field';
import FieldHtmlProperties from './FieldHtmlProperties';

export default class AssociatedField extends Field {
	constructor(
		name: string,
		associated_field: Field,
		field_html_properties: FieldHtmlProperties = FieldHtmlProperties.defaults()
	) {
		super(name, '', associated_field, field_html_properties);
	}
}
