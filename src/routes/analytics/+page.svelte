<script lang="ts">
	import FiltersSelector from '$lib/admin/components/PaginatedTable/FiltersSelector.svelte';
	import {getSavedFilters} from '$lib/admin/src/filters';
	import {onMount} from 'svelte';
	import SavedFilter from '$lib/admin/SavedFilter';
	import Operation, {operations_filters} from '$lib/entities/Operation';
	import {getOperationsForAnalytics} from '$lib/db/operations';
	import {Line} from 'svelte-chartjs';
	import YearlyTotals from "../../lib/operation_graphs/YearlyTotals";
	import YearMonthTags from "../../lib/operation_graphs/YearMonthTags";
	import MonthlyTotals from "../../lib/operation_graphs/MonthlyTotals";
	import GraphData from "$lib/graphs/GraphData";
	import MultipleGraphData from "$lib/graphs/MultipleGraphData";
	import AbstractGraph from "$lib/graphs/AbstractGraph";

	const graph_types = [
		YearlyTotals,
		MonthlyTotals,
		YearMonthTags,
	];

	let filters: Array<SavedFilter> = [];
	let current_filter: SavedFilter | null = null;
	let operations: Array<Operation> = [];
	let current_graph_type: AbstractGraph = null;

	let charts: Array<GraphData> = []
	let chart_component = false;
	let chart_data = {};
	let chart_options = {};

	// Handling of MultipleGraphData instances
	let multiple_charts = false;
	let chart_to_diplay_index: number|null = null;
	let discriminant_name_to_display: String|null = null;

	onMount(() => {
		filters = getSavedFilters('operations');
	});

	async function changeFilter(event: CustomEvent) {
		const selected_filter: SavedFilter | null = event.detail;

		console.info('changed filter', selected_filter);

		if (!selected_filter) {
			operations = [];
			return;
		}

		current_filter = selected_filter;

		operations = await getOperationsForAnalytics(selected_filter);

		showGraph();
	}

	function changeMultipleGraphDisplay() {
		showGraph();
	}

	function changeGraph() {
		showGraph();
	}

	function showGraph() {
		if (current_graph_type && current_graph_type.getName()) {
			const graph: AbstractGraph = new current_graph_type(operations);
			const data = graph.getData();
			chart_component = Line;
			charts = [];
			if (data instanceof GraphData) {
				multiple_charts = false;
				chart_to_diplay_index = null;
				discriminant_name_to_display = null;
				chart_data = JSON.parse(JSON.stringify(data));
			} else if (data instanceof MultipleGraphData) {
				multiple_charts = true;
				for (const graph of data.graphs) {
					charts.push(graph);
				}
				if (chart_to_diplay_index === null) {
					chart_to_diplay_index = 0;
				}
				if (discriminant_name_to_display === null) {
					discriminant_name_to_display = data.discriminant_display_name;
				}
				chart_data = JSON.parse(JSON.stringify(data.graphs[chart_to_diplay_index]));
			} else {
				throw new Error(`Invalid graph type "${current_graph_type}".`);
			}
		}
	}
</script>

<h1>Analytics (Work in progress)</h1>

<hr />

<FiltersSelector
	config_filters={operations_filters}
	id="operations"
	on:filter-select={changeFilter}
/>

<hr />

<div class="row">
	<label for="available_graph_types" class="col-form-label col-sm-4">
		Available graph types:
	</label>
	<div class="col-sm-8">
		<select
			id="available_graph_types"
			class="form-control"
			bind:value={current_graph_type}
			on:change={changeGraph}
		>
			<option value={null} selected={current_filter === null}>- Choose a filter -</option>
			{#each graph_types as graph_type}
				<option value={graph_type}>
					{graph_type.getName()}
				</option>
			{/each}
		</select>
	</div>
</div>

{#if current_filter}
	<h3>Selected: {current_filter.name}</h3>
{/if}

{#if multiple_charts === true}
	<div class="row">
		<label for="available_graph_types" class="col-form-label col-sm-3">
			Select {discriminant_name_to_display} to display:
		</label>
		<div class="col-sm-4">
			<select
					name="charts_selector"
					id="charts_selector"
					class="form-control"
					bind:value={chart_to_diplay_index}
					on:change={changeMultipleGraphDisplay}
			>
				<option value="">- Select a chart to display -</option>
				{#each charts as chart, key (key)}
					<option value={key}>{chart.name}</option>
				{/each}
			</select>
		</div>
	</div>
	<svelte:component this={chart_component} data={chart_data} options={chart_options} />
{:else}
	<svelte:component this={chart_component} data={chart_data} options={chart_options} />
{/if}
