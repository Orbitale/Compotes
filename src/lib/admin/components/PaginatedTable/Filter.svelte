<script lang="ts">
	import {DateInput, localeFromDateFnsLocale} from 'date-picker-svelte';
	import enGB from 'date-fns/locale/en-GB/index.js';
	import {DateTime} from 'luxon';
	import ConfigFilter from '../../ConfigFilter';
	import FilterType from '../../FilterType';
	import FilterWithValue from '../../FilterWithValue';

	export let filter: ConfigFilter;
	export let change_callback: Function;

	let locale = localeFromDateFnsLocale(enGB);

	export function clear() {
		value = '';
		value1 = value2 = null;
	}

	export function setValue(filter_with_value: FilterWithValue|null) {
		if (!filter_with_value) {
			clear();

			return;
		}

		if (filter_with_value.name !== filter.name) {
			throw new Error('Invalid filter to set values.');
		}

		if (filter.type === FilterType.text) {
			value = filter_with_value.value;
		} else if (filter.type === FilterType.tags) {
			value = filter_with_value.value;
		} else if (filter.type === FilterType.date) {
			const split = filter_with_value.value.split(';');

			value = filter_with_value.value;
			value1 = new Date(split[0]);
			value2 = new Date(split[1]);
		} else if (filter.type === FilterType.number) {
			const split = filter_with_value.value.split(';');
			value = filter_with_value.value;

			value1 = parseFilterValueNumber(split[0]);
			value2 = parseFilterValueNumber(split[1]);

			if (value2 !== '' && parseInt(value1) > parseInt(value2)) {
				value1 = value2;
			}
		} else {
			console.error('Unsupported filter type', filter);
		}
	}

	// Final value to be sent to the callback function.
	let value: string = '';

	// For two-value filters, like range or date.
	// They will be concatenated as "value1;value2" in the final value.
	let value1: any = null;
	let value2: any = null;

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

		// Only between min and max int.
		if (parsed > 9223372036854775) {
			parsed = 9223372036854775;
		}
		if (parsed < -9223372036854775) {
			parsed = -9223372036854775;
		}

		return parsed.toString(10);
	}

	function getStringFromDate(date: Date): string {
		return DateTime.fromISO(date.toISOString()).toFormat('yyyy-MM-dd');
	}

	function updateValueFromValues() {
		if (filter.type === FilterType.date) {
			value1 = value1 ? value1 : new Date(0);
			value2 = value2 ? value2 : new Date();
			value = getStringFromDate(value1)+";"+getStringFromDate(value2);
		} else if (filter.type === FilterType.number) {
			value1 = parseFilterValueNumber(value1);
			value2 = parseFilterValueNumber(value2);

			if (value2 !== '' && parseInt(value1) > parseInt(value2)) {
				value1 = value2;
			}

			value = `${value1};${value2}`;
		} else {
			console.error('Unsupported filter type', filter);
		}

		onChange();
	}

	if (!filter) {
		throw new Error('Must specify a filter object to create a Filter component.');
	}
</script>

<div class="filter-row row">
	<label for="input_filter_{filter.name}" class="col-sm-2 col-form-label">
		{filter.title || filter.name}
	</label>
	<div class="col-sm-10">
		{#if filter.type === FilterType.text}
			<input
				id="input_filter_{filter.name}"
				class="form-control"
				type="text"
				placeholder="{filter.title || filter.name}"
				on:change={onChange}
				bind:value
			/>
		{:else if filter.type === FilterType.date}
			<div class="row">
				<div class="col-sm-6">
					<DateInput
						min={new Date(new Date().getFullYear() - 50, 0, 1)}
						bind:value={value1}
						{locale}
						closeOnSelection={true}
						placeholder="After {filter.title || filter.name}"
						format="yyyy-MM-dd"
						on:select={updateValueFromValues}
					/>
				</div>
				<div class="col-sm-6">
					<DateInput
						min={new Date(new Date().getFullYear() - 50, 0, 1)}
						bind:value={value2}
						{locale}
						closeOnSelection={true}
						placeholder="Before {filter.title || filter.name}"
						format="yyyy-MM-dd"
						on:select={updateValueFromValues}
					/>
				</div>
			</div>
		{:else if filter.type === FilterType.number}
			<div class="row">
				<div class="col-sm-6">
					<input
						class="form-control"
						type="number"
						placeholder="Minimum {filter.title || filter.name}"
						on:change={updateValueFromValues}
						bind:value={value1}
					/>
				</div>
				<div class="col-sm-6">
					<input
						class="form-control"
						type="number"
						placeholder="Maximum {filter.title || filter.name}"
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
				placeholder="{filter.title || filter.name}"
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

<style lang="scss" global>
	@import "bootstrap/scss/bootstrap-utilities";
	@import "bootstrap/scss/forms/form-control";
	.date-time-field {
		input {
			@extend .form-control;
		}
	}
	.filter-row {
		margin-bottom: 1rem;
	}
</style>