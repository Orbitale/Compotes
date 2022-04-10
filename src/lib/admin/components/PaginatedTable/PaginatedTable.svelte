<script lang="ts">
    import ItemLine from "./ItemLine.svelte";
    import {onMount} from "svelte";
    import type {Writable} from "svelte/store";
    import SpinLoader from "../SpinLoader.svelte";
    import Field from "../../Field";
    import PageHooks from "../../PageHooks";
    import UrlAction from "../../UrlAction";
    import IteamHeadCell from "$lib/admin/components/PaginatedTable/IteamHeadCell.svelte";
    import SortableField from "$lib/admin/SortableField";
    import ConfigFilter from "$lib/admin/ConfigFilter";
    import FilterWithValue from "$lib/admin/FilterWithValue";
    import Filter from "$lib/admin/components/PaginatedTable/Filter.svelte";

    export let items_store: Writable<any>;
    export let fields: Array<Field>;
    export let actions: UrlAction[] = [];
    export let filters: Array<ConfigFilter> = [];
    export let page_hooks: PageHooks = null;
    export let sort_field_callback: Function = null;

    if(!fields || !fields.length) {
        throw new Error('No fields were configured for this view.');
    }

    let number_per_page = 20;
    let page = 1;
    let number_of_pages = 1;
    let store_executed_at_least_once = false;
    let current_sort_field: SortableField|null = null;
    let filters_with_values: Array<FilterWithValue> = [];

    items_store.subscribe(async (results) => {
        if (results && results.length) {
            await configureNumberOfPages();
        }
    });

    onMount(async () => {
        await firstPage();
        await configureNumberOfPages();
    });

    async function sortField(field: Field) {
        current_sort_field = field.sortable_field;
        if (sort_field_callback) {
            await sort_field_callback(page, field);
        }
    }

    async function updateFilter(filter: ConfigFilter, value: string) {
        // Remove filter
        filters_with_values = filters_with_values.filter((f: FilterWithValue) => f.name !== filter.name);

        if (value) {
            // Add filter only if it has a value
            filters_with_values.push(FilterWithValue.fromFilter(filter, value));
        }

        await page_hooks.callForItems(page, current_sort_field, filters_with_values);
    }

    async function configureNumberOfPages() {
        if ($items_store === null || $items_store === undefined) {
            number_of_pages = 1;
            return;
        }

        const countCb = page_hooks.hasCountCallback ? page_hooks.getCountCallback() : ($items_store||[]).length;

        let count: any = 0;

        if (countCb instanceof Promise) {
            count = await countCb;
            if (typeof count === 'function') {
                count = count();
            }
        } else if (typeof countCb === 'function') {
            count = countCb();
        } else {
            throw new Error('Count callback has an unexpected type');
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

        await page_hooks.callForItems(page, current_sort_field, filters_with_values);
    }

    async function nextPage() {
        page++;
        if (page > number_of_pages) {
            page = number_of_pages;
        }

        store_executed_at_least_once = false;
        await page_hooks.callForItems(page, current_sort_field, filters_with_values);
    }

    async function previousPage() {
        page--;
        if (page < 1) {
            page = 1;
        }
        await page_hooks.callForItems(page, current_sort_field, filters_with_values);
    }
</script>

<style lang="scss">
  #paginated-table {
    font-size: 0.8em;
  }
  .actions-header {
    text-align: center;
  }
  #previous-page, #next-page {
    font-size: 2.5em;
    width: 2.5em;
    height: 2.5em;
  }
  #pages-text {
    text-align: center;
    line-height: 4em;
    font-size: 1.5em;
  }
  #previous-page {float: left;}
  #next-page {float: right;}

  #no_elements {
    padding: 16px;
  }

  #filters {
    padding: 5px 15px;
  }
</style>

<table id="paginated-table" class="table table-bordered table-responsive table-hover table-striped table-sm">
    <thead>
        <tr id="paginated-table-header">
            <td colspan="{fields.length + (actions.length ? 1 : 0)}">
                <button type="button" class="btn btn-outline-primary" disabled="{page === 1}" on:click={previousPage} id="previous-page">&lt;</button>
                <button type="button" class="btn btn-outline-primary" disabled="{page === number_of_pages}" on:click={nextPage} id="next-page">&gt;</button>
                <div id="pages-text">
                    Page: {page} / {number_of_pages}
                </div>
            </td>
        </tr>

        {#if filters.length}
            <tr id="paginated-table-filters">
                <td colspan="{fields.length + (actions.length ? 1 : 0)}">
                    <button class="btn btn-light" data-bs-toggle="collapse" href="#filters">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M3.853 54.87C10.47 40.9 24.54 32 40 32H472C487.5 32 501.5 40.9 508.1 54.87C514.8 68.84 512.7 85.37 502.1 97.33L320 320.9V448C320 460.1 313.2 471.2 302.3 476.6C291.5 482 278.5 480.9 268.8 473.6L204.8 425.6C196.7 419.6 192 410.1 192 400V320.9L9.042 97.33C-.745 85.37-2.765 68.84 3.854 54.87L3.853 54.87z"/></svg>
                        Filters
                    </button>
                    <div id="filters" class="collapse">
                        {#each filters as filter}
                            <Filter {filter} change_callback={updateFilter} />
                        {/each}
                        <button class="btn btn-info">🔍 Filter</button>
                    </div>
                </td>
            </tr>
        {/if}

        <tr id="paginated-table-header-fields">
            {#each fields as field}
                <IteamHeadCell {field} sort_callback={sortField}/>
            {/each}
            {#if actions.length}
                <th class="actions-header">Actions</th>
            {/if}
        </tr>
    </thead>

    <tbody>
        {#if $items_store === undefined || $items_store === null}
            <tr>
                <td colspan={fields.length+(actions.length ? 1 : 0)}>
                    <SpinLoader height={50} as_block={true} />
                </td>
            </tr>
        {/if}
        {#key $items_store}
            {#each $items_store||[] as item, i}
                <ItemLine item={item} {fields} {actions} />
            {:else}
                <tr>
                    <td colSpan={fields.length+(actions.length ? 1 : 0)}>
                        <div id="no_elements">
                            No elements found.
                        </div>
                    </td>
                </tr>
            {/each}
        {/key}
    </tbody>
</table>
