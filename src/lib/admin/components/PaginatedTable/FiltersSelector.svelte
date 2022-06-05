<script lang="ts">
    import ConfigFilter from "../../ConfigFilter";
    import FilterWithValue from "../../FilterWithValue";
    import Filter from "./Filter.svelte";
    import SavedFilter from "../../SavedFilter";
    import {getByName, getSavedFilters, saveFilter} from "../../src/filters";
    import {createEventDispatcher, onMount} from 'svelte';

    const dispatch = createEventDispatcher();

    export let id: string;
    export let config_filters: Array<ConfigFilter> = [];

    let filters_visible: boolean = false;
    let filters_with_values: Array<FilterWithValue> = [];
    let disable_save_filters: boolean = true;
    let current_filter_name: string = '';
    let is_filter_name_invalid: boolean = false;
    let selected_filter: any = '';
    let saved_filters: Array<SavedFilter> = [];

	onMount(async () => {
		saved_filters = getSavedFilters(id);
	});

    function toggleFiltersDisplay() {
        filters_visible = !filters_visible;
    }

    async function clearFilters() {
        is_filter_name_invalid = false;
        current_filter_name = '';
        selected_filter = '';
        filters_with_values = [];

        config_filters.forEach((filter: ConfigFilter) => {
            if (filter.element) {
                filter.element.clear();
            } else {
                throw new Error(`ConfigFilter with name "${filter.name}" does not have a connected instance`);
            }
        });
    }

    async function selectFilter(event) {
        const name = event.target.value;

        if (!name) {
            await clearFilters();

            dispatch('filter-select', null);

            return;
        }

        const filter = getByName(id, name);

        current_filter_name = filter.name;
        filters_with_values = filter.deserialized_filters;

        config_filters.forEach((config_filter: ConfigFilter) => {
            if (!config_filter.element) {
                throw new Error('Cannot update filter value if elemente is not set.');
            }

            let value_index = filters_with_values.findIndex((fv: FilterWithValue) => fv.name === config_filter.name);

            const value = value_index < 0 ? null : filters_with_values[value_index];

            config_filter.element.setValue(value);
        });

        disable_save_filters = !!filter.name.match(/^</g);

        dispatch('filter-select', filter);

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

        config_filters.forEach((config_filter: ConfigFilter) => {
            let value_index = filters_with_values.findIndex((fv: FilterWithValue) => fv.name === config_filter.name);

            config_filter.value = value_index < 0 ? null : filters_with_values[value_index];
        });

        dispatch('filters-call', filters_with_values);
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

</script>

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
    {#each config_filters as filter}
        <Filter {filter} bind:this={filter.element} change_callback={updateFilter} />
    {/each}
    <br>
    <div class="d-flex" id="filters_actions_container">
        <button class="btn btn-outline-secondary ms-auto" on:click={saveFilters} disabled={disable_save_filters}>💾 Save filters</button>
        <input class="form-control" type="text" id="filter_name" placeholder="Filter name" class:is-invalid={is_filter_name_invalid} bind:value={current_filter_name}>
    </div>
</div>

<style lang="scss">
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
