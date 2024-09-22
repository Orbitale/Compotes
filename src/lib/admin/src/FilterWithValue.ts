import type ConfigFilter from './ConfigFilter';
import type FilterType from './FilterType';

export default class FilterWithValue {
	public readonly name: string;
	public readonly type: FilterType;
	public readonly value: string;

	constructor(name: string, type: FilterType, value: string) {
		this.name = name;
		this.type = type;
		this.value = value;
	}

	public static fromFilter(filter: ConfigFilter, value: string): FilterWithValue {
		return new FilterWithValue(filter.name, filter.type, value);
	}

	public static fromSerialized(filter: Partial<FilterWithValue>): FilterWithValue {
		if (!filter.name || !filter.type || !filter.value) {
			console.error('Serialized filter is incomplete', filter);

			throw new Error('Serialized filter is incomplete');
		}

		return new FilterWithValue(filter.name, filter.type, filter.value);
	}
}
