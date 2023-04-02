import DefaultAction from './DefaultAction';
import type ActionParams from './ActionParams';
import type Field from './Field';

export default class UrlAction extends DefaultAction {
	private readonly params: ActionParams;
	private readonly _url: string;

	constructor(name: string, url: string, params: ActionParams) {
		super(name);
		this.params = params;
		this._url = url;
	}

	public url(item: object): string {
		let url = this._url;

		this.params.params.forEach((field: Field) => {
			url = url.replace(`:${field.name}`, field.displayFromItem(item).toString());
		});

		return `${url}`;
	}
}
