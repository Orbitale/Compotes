import type SortableField from '$lib/admin/SortableField';
import type FilterWithValue from '$lib/admin/FilterWithValue';

export default class PageHooks {
	private readonly _items_callback: Function;
	private readonly _count_callback: Function;

	constructor(items_callback: Function, count_callback: Function = null) {
		this._items_callback = items_callback;
		this._count_callback = count_callback || null;
	}

	get hasCountCallback(): boolean {
		return this._count_callback !== null;
	}

	public callForItems(
		page: number,
		field: SortableField | null,
		filters: Array<FilterWithValue> | null
	): void {
		this._items_callback(page, field, filters);
	}

	public getCountCallback(): Function {
		return this._count_callback();
	}
}
