import type FilterType from './FilterType';
import FilterWithValue from './FilterWithValue';
import Filter from './components/PaginatedTable/Filter.svelte';

export default class ConfigFilter {
	public readonly name: string;
	public readonly title: string;
	public readonly type: FilterType;
	public element: Filter | null;
	public value: FilterWithValue | null;

	constructor(name: string, title: string, type: FilterType) {
		this.name = name;
		this.title = title;
		this.type = type;
	}
}
