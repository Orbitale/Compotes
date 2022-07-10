import SavedFilter from '../SavedFilter';
import { getConfig } from './config';

export function getByName(save_key: string, name: string): SavedFilter {
	const filters = getSavedFilters(save_key);

	const filtered_by_name = filters.filter((filter: SavedFilter) => filter.name === name);

	if (!filtered_by_name.length) {
		throw new Error(`Filter with name "${name}" was not found.`);
	} else if (filtered_by_name.length > 1) {
		throw new Error(`Found multiple filters with name "${name}"`);
	}

	return filtered_by_name[0];
}

export function getSavedFilters(
	save_key: string,
	with_builtin: boolean = true
): Array<SavedFilter> {
	let stored_filters = localStorage.getItem('compotes_filters_' + save_key);

	if (!stored_filters) {
		stored_filters = '[]';
	}

	let deserialized_filters: Array<SavedFilter> = JSON.parse(stored_filters);

	if (!Array.isArray(deserialized_filters)) {
		console.error('Stored filters are not stored as an array. Resetting them.');
		deserialized_filters = [];
	}

	const builtin = with_builtin ? getConfig().builtinFilters[save_key] || [] : [];

	return [
		...builtin,
		...deserialized_filters.map((f: SavedFilter) => {
			return SavedFilter.fromSerialized(f.name, f.filters);
		})
	];
}

export function saveFilter(save_key: string, new_filter: SavedFilter) {
	let deserialized_filters = getSavedFilters(save_key, false);

	let existing_filter_index = deserialized_filters.findIndex((filter: SavedFilter) => {
		return filter.name === new_filter.name;
	});

	if (existing_filter_index >= 0) {
		deserialized_filters[existing_filter_index] = new_filter;
	} else {
		deserialized_filters.push(new_filter);
	}

	localStorage.setItem('compotes_filters_' + save_key, JSON.stringify(deserialized_filters));
}
