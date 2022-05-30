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

    const chart_data = {
        labels: ['label 1', 'label 2', 'label 3', '', ''],
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

    const chart_options = {};

    onMount(() => {
        filters = getSavedFilters('operations');
    });

    async function changeFilter() {
        if (!selected_filter) {
            operations = [];
            return;
        }

        operations = await getOperationsForAnalytics(selected_filter);
    }
</script>

<h1>Analytics (Work in progress)</h1>

<hr>

<h2>Available filters:</h2>
<select id="available_filters" bind:value={selected_filter} on:change={changeFilter}>
    <option value={null} selected={selected_filter === null}>- Choose a filter -</option>
    {#each filters as filter}
        <option value={filter}>
            {filter.name}
        </option>
    {/each}
</select>

{#if selected_filter}
    <h3>Selected: {selected_filter.name}</h3>
{/if}

<Line data={chart_data} options={chart_options} />
