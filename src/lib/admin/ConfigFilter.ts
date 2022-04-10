import type FilterType from '$lib/admin/FilterType';

export default class ConfigFilter {
	public readonly name: string;
	public readonly title: string;
	public readonly type: FilterType;

	constructor(name: string, title: string, type: FilterType) {
		this.name = name;
		this.title = title;
		this.type = type;
	}
}
