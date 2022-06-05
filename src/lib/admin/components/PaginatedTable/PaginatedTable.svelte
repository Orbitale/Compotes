<script lang="ts">
	import FiltersSelector from './FiltersSelector.svelte';
	import ItemLine from './ItemLine.svelte';
	import ItemHeadCell from './ItemHeadCell.svelte';
	import SpinLoader from '../SpinLoader.svelte';

	import {onMount} from 'svelte';
	import type {Readable} from 'svelte/store';

	import Field from '../../Field';
	import PageHooks from '../../PageHooks';
	import UrlAction from '../../UrlAction';
	import SortableField from '../../SortableField';
	import ConfigFilter from '../../ConfigFilter';
	import FilterWithValue from '../../FilterWithValue';

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
	let normalized_filters: Array<FilterWithValue> = [];

	items_store.subscribe(async (results) => {
		if (results && results.length) {
			await configureNumberOfPages();
		}
	});

	onMount(async () => {
		await firstPage();
	});

	async function sortField(field: Field) {
		current_sort_field = field.sortable_field;
		if (sort_field_callback) {
			await sort_field_callback(page, field);
		}
	}

	async function configureNumberOfPages() {
		if ($items_store === null || $items_store === undefined) {
			number_of_pages = 1;
			return;
		}

		const countCb = page_hooks.hasCountCallback
			? page_hooks.getCountCallback()
			: ($items_store || []).length;

		let count: any = 0;

		if (typeof countCb === 'function') {
			count = await countCb(normalized_filters);
		} else if (typeof countCb === 'number') {
			count = countCb;
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

	async function callFilters(event: CustomEvent) {
		const filters_with_values: Array<FilterWithValue> = event.detail || [];
		normalized_filters = filters_with_values.map((f: FilterWithValue) => { return {...f}; });

		await fetchItems();
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
		await page_hooks.callForItems(page, current_sort_field, normalized_filters);
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
			<tr>
				<td colspan={fields.length + (actions.length ? 1 : 0)}>
					<FiltersSelector id={id} config_filters={filters} on:filters-call={callFilters} />
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
</style>
