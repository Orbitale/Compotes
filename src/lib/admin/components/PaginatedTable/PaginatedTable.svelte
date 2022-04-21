
<script lang="ts">
	import Filter from './Filter.svelte';
	import ItemLine from './ItemLine.svelte';
	import ItemHeadCell from './ItemHeadCell.svelte';
	import SpinLoader from '../SpinLoader.svelte';

	import {onMount} from 'svelte';
	import type {Readable} from 'svelte/store';
	import {getByName, getSavedFilters, saveFilter} from '../../src/filters';

	import Field from '../../Field';
	import PageHooks from '../../PageHooks';
	import UrlAction from '../../UrlAction';
	import SortableField from '../../SortableField';
	import ConfigFilter from '../../ConfigFilter';
	import FilterWithValue from '../../FilterWithValue';
	import SavedFilter from '../../SavedFilter';

	export let id: string;
	export let items_store: Readable<any>;
	export let fields: Array<Field>;
	export let actions: UrlAction[] = [];
	export let filters: Array<ConfigFilter> = [];
	export let page_hooks: PageHooks = null;
	export let sort_field_callback: Function = null;

	if (!id) {
		throw new Error('You must specify an ID for your Paginated Table.');
	}

	if (!fields || !fields.length) {
		throw new Error('No fields were configured for this view.');
	}

	if (!items_store) {
		throw new Error('You must configure a data store to create a PaginatedTable.');
	}

	let number_per_page = 20;
	let page = 1;
	let number_of_pages = 1;
	let store_executed_at_least_once = false;
	let current_sort_field: SortableField | null = null;
	let saved_filters: Array<SavedFilter> = [];
	let filters_with_values: Array<FilterWithValue> = [];
	let disable_save_filters: boolean = true;
	let current_filter_name: string = '';
	let is_filter_name_invalid: boolean = false;
	let selected_filter: any = '';
	let filters_visible: boolean = false;

	items_store.subscribe(async (results) => {
		if (results && results.length) {
			await configureNumberOfPages();
		}
	});

	onMount(async () => {
		await firstPage();
		saved_filters = getSavedFilters(id);
	});

	async function sortField(field: Field) {
		current_sort_field = field.sortable_field;
		if (sort_field_callback) {
			await sort_field_callback(page, field);
		}
	}

	async function selectFilter(event) {
		const name = event.target.value;

		console.info({selected_filter});
		if (!name) {
			await clearFilters();
			return;
		}

		const filter = getByName(id, name);

		current_filter_name = filter.name;
		filters_with_values = filter.deserialized_filters;

		filters.forEach((config_filter: ConfigFilter) => {
			if (!config_filter.element) {
				throw new Error('Cannot update filter value if elemente is not set.');
			}

			let value_index = filters_with_values.findIndex((fv: FilterWithValue) => fv.name === config_filter.name);

			const value = value_index < 0 ? null : filters_with_values[value_index];

			config_filter.element.setValue(value);
		});

		await callFilters();
	}

	async function updateFilter(filter: ConfigFilter, value: string) {
		// Remove filter
		filters_with_values = filters_with_values.filter(
			(f: FilterWithValue) => f.name !== filter.name
		);

		if (value) {
			// Add filter only if it has a value
			filters_with_values.push(FilterWithValue.fromFilter(filter, value));
		}

		await callFilters();
	}

	async function callFilters() {
		disable_save_filters = filters_with_values.length === 0;

		filters.forEach((config_filter: ConfigFilter) => {
			let value_index = filters_with_values.findIndex((fv: FilterWithValue) => fv.name === config_filter.name);

			config_filter.value = value_index < 0 ? null : filters_with_values[value_index];
		});

		await fetchItems();
	}

	async function saveFilters() {
		if (!current_filter_name) {
			is_filter_name_invalid = true;
			return;
		}
		is_filter_name_invalid = false;

		if (filters_with_values.length === 0) {
			console.error('Called "Save filters" with no filters set.');
			return;
		}

		await saveFilter(id, new SavedFilter(current_filter_name, filters_with_values));
	}

	async function clearFilters() {
		is_filter_name_invalid = false;
		current_filter_name = '';
		selected_filter = '';
		filters_with_values = [];

		filters.forEach((filter: ConfigFilter) => {
			if (filter.element) {
				filter.element.clear();
			} else {
				throw new Error(`ConfigFilter with name "${filter.name}" does not have a connected instance`);
			}
		});

		await callFilters();
	}

	async function configureNumberOfPages() {
		if ($items_store === null || $items_store === undefined) {
			number_of_pages = 1;
			return;
		}

		const normalized_filters = filters_with_values.map((f: FilterWithValue) => { return {...f}; });

		const countCb = page_hooks.hasCountCallback
			? page_hooks.getCountCallback()
			: ($items_store || []).length;

		let count: any = 0;

		if (typeof countCb === 'function') {
			count = await countCb(normalized_filters);
		} else {
			throw new Error(`Count callback has an unexpected type "${typeof countCb}".`);
		}

		if (isNaN(parseInt(count, 10))) {
			throw new Error('Could not determine the number of pages for this view.');
		}

		number_of_pages = Math.ceil(count / number_per_page);
		if (number_of_pages === 0) {
			number_of_pages = 1;
		}
	}

	async function firstPage() {
		page = 1;

		await fetchItems();
	}

	async function nextPage() {
		page++;
		if (page > number_of_pages) {
			page = number_of_pages;
		}

		store_executed_at_least_once = false;
		await fetchItems();
	}

	async function previousPage() {
		page--;
		if (page < 1) {
			page = 1;
		}
		await fetchItems();
	}

	async function fetchItems() {
		const normalized_filters = filters_with_values.map((f: FilterWithValue) => { return {...f}; });

		await page_hooks.callForItems(page, current_sort_field, normalized_filters);
	}

	function toggleFiltersDisplay() {
		filters_visible = !filters_visible;
	}
</script>

<table
	id="paginated-table"
	class="table table-bordered table-responsive table-hover table-striped table-sm"
>
	<thead>
		<tr id="paginated-table-header">
			<td colspan={fields.length + (actions.length ? 1 : 0)}>
				<button
					type="button"
					class="btn btn-outline-primary"
					disabled={page === 1}
					on:click={previousPage}
					id="previous-page"
				>&lt;</button>
				<button
					type="button"
					class="btn btn-outline-primary"
					disabled={page === number_of_pages}
					on:click={nextPage}
					id="next-page"
				>&gt;</button>
				<div id="pages-text">
					Page: {page} / {number_of_pages}
				</div>
			</td>
		</tr>

		{#if filters.length}
			<tr id="paginated-table-filters">
				<td colspan={fields.length + (actions.length ? 1 : 0)}>
					<div class="d-flex">
						<button
							class="btn mr5"
							class:btn-primary={filters_visible === true}
							class:btn-outline-primary={filters_visible === false}
							data-bs-toggle="collapse"
							href="#filters"
							on:click={toggleFiltersDisplay}
						>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15">
								<!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
								<path d="M3.853 54.87C10.47 40.9 24.54 32 40 32H472C487.5 32 501.5 40.9 508.1 54.87C514.8 68.84 512.7 85.37 502.1 97.33L320 320.9V448C320 460.1 313.2 471.2 302.3 476.6C291.5 482 278.5 480.9 268.8 473.6L204.8 425.6C196.7 419.6 192 410.1 192 400V320.9L9.042 97.33C-.745 85.37-2.765 68.84 3.854 54.87L3.853 54.87z"/>
							</svg>
							{filters_visible ? 'Hide' : 'Show'} filters
						</button>
						<button class="btn btn-outline-dark" on:click={clearFilters}>🗑 Clear filters</button>
						<select name="filters_select" id="filters_select" class="form-control ms-auto" bind:value={selected_filter} on:change={selectFilter}>
							<option value="">- Select a filter -</option>
							{#each saved_filters as filter}
								<option value={filter.name}>{filter.name}</option>
							{/each}
						</select>
					</div>
					<div id="filters" class="collapse">
						{#each filters as filter}
							<Filter {filter} bind:this={filter.element} change_callback={updateFilter} />
						{/each}
						<br>
						<div class="d-flex" id="filters_actions_container">
							<button class="btn btn-outline-secondary ms-auto" on:click={saveFilters} disabled={disable_save_filters}>💾 Save filters</button>
							<input class="form-control" type="text" id="filter_name" placeholder="Filter name" class:is-invalid={is_filter_name_invalid} bind:value={current_filter_name}>
						</div>
					</div>
				</td>
			</tr>
		{/if}

		<tr id="paginated-table-header-fields">
			{#each fields as field}
				<ItemHeadCell {field} sort_callback={sortField} />
			{/each}
			{#if actions.length}
				<th class="actions-header">Actions</th>
			{/if}
		</tr>
	</thead>

	<tbody>
		{#if $items_store === undefined || $items_store === null}
			<tr>
				<td colspan={fields.length + (actions.length ? 1 : 0)}>
					<SpinLoader height={50} as_block={true} />
				</td>
			</tr>
		{/if}
		{#key $items_store}
			{#each $items_store || [] as item, i}
				<ItemLine {item} {fields} {actions} />
			{:else}
				<tr>
					<td colSpan={fields.length + (actions.length ? 1 : 0)}>
						<div id="no_elements">No elements found.</div>
					</td>
				</tr>
			{/each}
		{/key}
	</tbody>
</table>

<style lang="scss">
	#paginated-table {
		font-size: 0.8em;
	}
	.actions-header {
		text-align: center;
	}
	#previous-page,
	#next-page {
		font-size: 2.5em;
		width: 2.5em;
		height: 2.5em;
	}
	#pages-text {
		text-align: center;
		line-height: 4em;
		font-size: 1.5em;
	}
	#previous-page {
		float: left;
	}
	#next-page {
		float: right;
	}

	#no_elements {
		padding: 16px;
	}

	#filters {
		padding: 5px 15px;
	}

	#filters_select {
		width: 200px;
	}

	#filter_name {
		width: 150px;
	}

	.mr5 {
		margin-right: 5px;
	}

	#filters_actions_container button {
		margin-right: 5px;
	}
</style>
