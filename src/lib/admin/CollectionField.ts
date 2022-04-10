import type AssociatedField from './AssociatedField';
import Field from './Field';
import FieldOptions from '$lib/admin/FieldOptions';

export default class CollectionField extends Field {
	constructor(name: string, text: string, item_field: Field) {
		super(name, text, new FieldOptions(item_field));
	}

	public displayFromItem(item: object | any): any {
		let field: Field | AssociatedField;

		const items = item[this.name];

		if (items.length === 0 || !(items instanceof Array)) {
			return null;
		}

		field = this._associated_field;

		return items.map((singleItem) => field.displayFromItem(singleItem));
	}
}
