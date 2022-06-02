<script lang="ts">
    import {getSavedFilters} from "$lib/admin/src/filters.ts";
    import {onMount} from "svelte";
    import SavedFilter from "$lib/admin/SavedFilter.ts";
    import Operation from "../lib/entities/Operation.ts";
    import {getOperationsForAnalytics} from "../lib/db/operations.ts";
    import Line from "svelte-chartjs/src/Line.svelte"

    let filters: Array<SavedFilter> = [];
    let selected_filter: SavedFilter|null = null;
    let operations: Array<Operation> = [];

    let chart_component = false;
    let chart_data = {};
    let chart_options = {};

    onMount(() => {
        filters = getSavedFilters('operations');
    });

    async function changeFilter() {
        if (!selected_filter) {
            operations = [];
            return;
        }

        operations = await getOperationsForAnalytics(selected_filter);

        let series = [];

        for (const operation of operations) {
            // todo: add data to series
        }

        chart_component = Line;
        chart_data = {
            labels: ['label 1', 'label 2', 'label 3', 'lab 4', 'lab 5'],
            datasets: [
                {
                    label: "Operations",
                    data: [1, 2, 3, 2, 1],
                    backgroundColor: "rgba(255, 99, 132, 0.2)",
                    borderColor: "rgba(255, 99, 132, 1)",
                    borderWidth: 1
                }
            ]
        };
    }
</script>

<h1>Analytics (Work in progress)</h1>

<hr>

<div class="row">
    <label for="available_filters" class="col-form-label col-sm-2">
        Available filters:
    </label>
    <div class="col-sm-10">
        <select id="available_filters" class="form-control" bind:value={selected_filter} on:change={changeFilter}>
            <option value={null} selected={selected_filter === null}>- Choose a filter -</option>
            {#each filters as filter}
                <option value={filter}>
                    {filter.name}
                </option>
            {/each}
        </select>
    </div>
</div>

{#if selected_filter}
    <h3>Selected: {selected_filter.name}</h3>
{/if}

<svelte:component this={chart_component} data={chart_data} options={chart_options} />
