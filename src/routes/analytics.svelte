<script lang="ts">
    import FiltersSelector from "$lib/admin/components/PaginatedTable/FiltersSelector.svelte";
    import {getSavedFilters} from "$lib/admin/src/filters.ts";
    import {onMount} from "svelte";
    import SavedFilter from "$lib/admin/SavedFilter.ts";
    import Operation, {operations_filters} from '$lib/entities/Operation';
    import {getOperationsForAnalytics} from "$lib/db/operations.ts";
    import Line from "svelte-chartjs/src/Line.svelte"
    import YearlyTotals from "$lib/graphs/YearlyTotals.ts";
    import MonthlyTotals from "$lib/graphs/MonthlyTotals.ts";
    import type Graph from "$lib/graphs/Graph.ts";

    const graph_types = [
        YearlyTotals,
        MonthlyTotals,
    ];

    let filters: Array<SavedFilter> = [];
    let current_filter: SavedFilter|null = null;
    let operations: Array<Operation> = [];
    let current_graph_type: Graph = null;

    let chart_component = false;
    let chart_data = {};
    let chart_options = {};

    onMount(() => {
        filters = getSavedFilters('operations');
    });

    async function changeFilter(event: CustomEvent) {
        debugger;
        const selected_filter: SavedFilter|null = event.detail;

        console.info('changed filter', selected_filter);

        if (!selected_filter) {
            operations = [];
            return;
        }

        current_filter = selected_filter;

        operations = await getOperationsForAnalytics(selected_filter);

        showGraph();
    }

    function changeGraph() {
        showGraph();
    }

    function showGraph() {
        if (current_graph_type && current_graph_type.name) {
            const graph: Graph = new current_graph_type(operations);
            const data = graph.getData();
            const normalized = JSON.parse(JSON.stringify(data));

            chart_component = Line;
            chart_data = normalized;
        }
    }
</script>

<h1>Analytics (Work in progress)</h1>

<hr>

<FiltersSelector config_filters={operations_filters} id="operations" on:filter-select={changeFilter} />

<hr>

<div class="row">
    <label for="available_graph_types" class="col-form-label col-sm-2">
        Available graph types:
    </label>
    <div class="col-sm-10">
        <select id="available_graph_types" class="form-control" bind:value={current_graph_type} on:change={changeGraph}>
            <option value={null} selected={current_filter === null}>- Choose a filter -</option>
            {#each graph_types as graph_type}
                <option value={graph_type}>
                    {graph_type.name}
                </option>
            {/each}
        </select>
    </div>
</div>

{#if current_filter}
    <h3>Selected: {current_filter.name}</h3>
{/if}

<svelte:component this={chart_component} data={chart_data} options={chart_options} />
