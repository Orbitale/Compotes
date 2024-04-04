<script lang="ts">
	import FiltersSelector from '$lib/admin/components/PaginatedTable/FiltersSelector.svelte';
	import { getSavedFilters } from '$lib/admin/src/filters';
	import {type ComponentType, onMount} from 'svelte';
	import type SavedFilter from '$lib/admin/src/SavedFilter';
	import Operation, { operations_filters } from '$lib/entities/Operation';
	import { getOperationsForAnalytics } from '$lib/db/operations';
	import { Line } from 'svelte-chartjs';
	import 'chart.js/auto';
	import YearlyTotals from '../../lib/operation_graphs/YearlyTotals';
	import YearMonthTags from '../../lib/operation_graphs/YearMonthTags';
	import MonthlyTotals from '../../lib/operation_graphs/MonthlyTotals';
	import GraphData from '$lib/graphs/GraphData';
	import MultipleGraphData from '$lib/graphs/MultipleGraphData';
	import type AbstractGraph from '$lib/graphs/AbstractGraph';

	const graph_types = [YearlyTotals, MonthlyTotals, YearMonthTags];

	let current_filter: SavedFilter | null = null;
	let operations: Array<Operation> = [];
	let current_graph_type: AbstractGraph|null = null;

	let charts: Array<GraphData> = [];
	let chart_component: ComponentType|null = null;
	let chart_data = {};
	let chart_options = {};

	// Handling of MultipleGraphData instances
	let multiple_charts = false;
	let chart_to_diplay_index: number | null = null;
	let discriminant_name_to_display: String | null = null;

	async function changeFilter(event: CustomEvent) {
		const selected_filter: SavedFilter | null = event.detail;

		console.info('changed filter', selected_filter);

		if (!selected_filter) {
			operations = [];
			return;
		}

		current_filter = selected_filter;

		operations = await getOperationsForAnalytics(selected_filter);

		if (!operations.length) {
			return;
		}

		showGraph();
	}

	async function callFilters(event: CustomEvent) {
		console.info('Called filters', event);
		console.info('event.detail', event.detail);
	}

	function changeMultipleGraphDisplay() {
		showGraph();
	}

	function changeGraph() {
		showGraph();
	}

	function showGraph() {
		if (!(current_graph_type && current_graph_type.getName())) {
			return;
		}

		const graph: AbstractGraph = new current_graph_type(operations);
		const data: MultipleGraphData | GraphData = graph.getData();
		chart_component = Line;
		charts = [];
		const type = data.type;
		if (type === GraphData.type) {
			multiple_charts = false;
			chart_to_diplay_index = null;
			discriminant_name_to_display = null;
			chart_data = JSON.parse(JSON.stringify(data));
		} else if (type === MultipleGraphData.type) {
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
			const final_data = data.graphs[chart_to_diplay_index];
			if (!final_data) {
				console.warn('No data to generate a graph');
				chart_data = [];
			}
			chart_data = JSON.parse(JSON.stringify(final_data));
		} else {
			throw new Error(
					`Invalid graph type "${current_graph_type}" (value type found: "${type}").`
			);
		}

		console.info('chart_data', chart_data);
	}
</script>

<h1>Analytics (Work in progress)</h1>

<hr />

<FiltersSelector
	config_filters={operations_filters}
	id="operations"
	on:filter-select={changeFilter}
	on:filters-call={callFilters}
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

{#if chart_data?.datasets}
	<h2>Data:</h2>

	<table class="table table-bordered table-striped table-hover">
		<thead class="thead-dark">
			<tr>
				<td>&nbsp;</td>
				{#each (chart_data?.labels||[]) as label}
					<th>{label}</th>
				{/each}
			</tr>
		</thead>
		<tbody>
			{#each (chart_data?.datasets||[]) as dataset}
				<tr>
					<td>{dataset.label}</td>
					{#each dataset.data as data}
						<td style="text-align: right;">
							<span data-toggle="tooltip" data-placement="top" title="{data}">
								{new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(data).toString()}
							</span>

						</td>
					{/each}
				</tr>
			{/each}
		</tbody>
	</table>
{/if}
