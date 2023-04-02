import FilterType from "./FilterType";
import type FilterWithValue from './FilterWithValue';
import type Filter from '../components/PaginatedTable/Filter.svelte';

export default class ConfigFilter {
	public readonly name: string;
	public readonly title: string;
	public readonly type: FilterType;
	public readonly options: {[key: string]: any};
	public element: Filter | null;
	public value: FilterWithValue | null;

	constructor(name: string, title: string, type: FilterType, options?: {[key: string]: any}) {
		this.name = name;
		this.title = title;
		this.type = type;
		this.options = options || {};
		this.validateOptions();
	}

	private validateOptions() {
		switch (this.type) {
			case FilterType.entity:
				if (!this.options.entities) {
					throw new Error('To define a filter of type "entity", you must also specify the "entities" option. This must contain an array that contains elements of type "{name: string, value: string}", or a callable that returns such array, or a promise that resolves to such array.');
				}
				break;
			default:
				break;
		}
	}
}
