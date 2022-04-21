import type SortableField from './SortableField';
import type FilterWithValue from './FilterWithValue';

export default class PageHooks {
	private readonly _items_callback: Function;
	private readonly _count_callback: Function;

	private lastPage: number = 1;
	private lastField: SortableField|null;
	private lastFilters: Array<FilterWithValue> | null;

	constructor(items_callback: Function, count_callback: Function = null) {
		this._items_callback = items_callback;
		this._count_callback = count_callback || null;
	}

	get hasCountCallback(): boolean {
		return this._count_callback !== null;
	}

	public refresh() {
		this.callForItems(this.lastPage, this.lastField, this.lastFilters);
	}

	public callForItems(
		page: number,
		field: SortableField | null,
		filters: Array<FilterWithValue> | null
	): void {
		this.lastPage = page;
		this.lastField = field;
		this.lastFilters = filters;
		this._items_callback(page, field, filters);
	}

	public getCountCallback(): Function {
		return this._count_callback;
	}
}
