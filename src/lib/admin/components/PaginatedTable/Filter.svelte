<script lang="ts">
	import ConfigFilter from '$lib/admin/ConfigFilter';
	import FilterType from '$lib/admin/FilterType';
	import DatePicker from '@beyonk/svelte-datepicker/src/components/DatePicker.svelte';
	import { dayjs } from '$lib/utils/date-utils';

	export let filter: ConfigFilter;
	export let change_callback: Function;

	export function clear() {
		value1 = value2 = value = '';
	}

	// Final value to be sent to the callback function.
	let value: string = '';

	// For two-value filters, like range or date.
	// They will be concatenated as "value1;value2" in the final value.
	let value1: string = '';
	let value2: string = '';

	function onChange() {
		if (change_callback) {
			change_callback(filter, value);
		}
	}

	function parseFilterValueNumber(string: null|string): string {
		if (string === null || (string.trim && string.trim() === '')) {
			return '';
		}

		let parsed = parseInt(string, 10);
		if (isNaN(parsed)) {
			return '';
		}

		if (parsed > 9223372036854775) {
			parsed = 9223372036854775;
		}
		if (parsed < -9223372036854775) {
			parsed = -9223372036854775;
		}

		return parsed.toString(10);
	}

	function updateValueFromValues() {
		if (filter.type === FilterType.date) {
			value1 = value1 ? dayjs(value1).format('YYYY-MM-DD') : dayjs().subtract(50, 'year').format('YYYY-MM-DD');
			value2 = value2 ? dayjs(value2).format('YYYY-MM-DD') : dayjs().add(1, 'day').format('YYYY-MM-DD');
		} else if (filter.type === FilterType.number) {
			value1 = parseFilterValueNumber(value1);
			value2 = parseFilterValueNumber(value2);
			if (value2 !== '' && parseInt(value1) > parseInt(value2)) {
				value1 = value2;
			}
		} else {
			console.error('Unsupported filter type', filter);
		}

		value = `${value1};${value2}`;
		onChange();
	}

	if (!filter) {
		throw new Error('Must specify a filter object to create a Filter component.');
	}
</script>

<div class="row">
	<label for="input_filter_{filter.name}" class=" col-form-label col-sm-2">
		{filter.title || filter.name}
	</label>
	<div class="col-sm-10">
		{#if filter.type === FilterType.text}
			<input
				id="input_filter_{filter.name}"
				class="form-control"
				type="text"
				on:change={onChange}
				bind:value
			/>
		{:else if filter.type === FilterType.date}
			<DatePicker
				start={dayjs().subtract(50, 'year')}
				end={dayjs().add(1, 'year')}
				bind:selected={value1}
				placeholder="After date"
				format="YYYY-MM-DD"
				on:change={updateValueFromValues}
			/>
			<DatePicker
				start={dayjs().subtract(50, 'year')}
				end={dayjs().add(1, 'year')}
				bind:selected={value2}
				placeholder="Before date"
				format="YYYY-MM-DD"
				on:change={updateValueFromValues}
			/>
		{:else if filter.type === FilterType.number}
			<div class="row">
				<div class="col-sm-6">
					Minimum:
					<input
						class="form-control"
						type="number"
						on:change={updateValueFromValues}
						bind:value={value1}
					/>
				</div>
				<div class="col-sm-6">
					Maximum:
					<input
						class="form-control"
						type="number"
						on:change={updateValueFromValues}
						bind:value={value2}
					/>
				</div>
			</div>
		{:else if filter.type === FilterType.tags}
			<input
				id="input_filter_{filter.name}"
				class="form-control"
				type="text"
				on:change={onChange}
				bind:value
			/>
		{:else}
			<h5>
				<span class="badge bg-danger">Unknown input type "{filter.type}"</span>
			</h5>
		{/if}
	</div>
</div>
