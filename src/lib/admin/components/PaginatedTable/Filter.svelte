<script lang="ts">
	import {DateInput, localeFromDateFnsLocale} from 'date-picker-svelte';
	import enGB from 'date-fns/locale/en-GB/index.js';
	import {DateTime} from 'luxon';
	import ConfigFilter from '../../src/ConfigFilter';
	import FilterType from '../../src/FilterType';
	import FilterWithValue from '../../src/FilterWithValue';
	import {createEventDispatcher, onMount} from "svelte";

	const dispatch = createEventDispatcher();

	export let filter: ConfigFilter;

	type SelectOption = {name: string, value: string};

	let options: Array<SelectOption> = [];

	let locale = localeFromDateFnsLocale(enGB);

	async function resolveAsyncValue(value: any) {
		if (value instanceof Promise) {
			return await resolveAsyncValue(await value);
		} else if (typeof value === 'function' && value.constructor.name === 'AsyncFunction') {
			return await resolveAsyncValue(await value());
		} else if (typeof value === 'function') {
			return await resolveAsyncValue(value());
		} else if (Array.isArray(value)) {
			return value;
		}

		throw new Error('Could not find type of "filter.options.entities". Must be either an array or a function.');
	}

	onMount(async () => {
		if (filter.type === FilterType.entity) {
			const entities: Array<SelectOption>|(() => Array<SelectOption>|Promise<Array<SelectOption>>) = filter.options.entities;

			options = await resolveAsyncValue(entities);

			options.map((i: any) => {
				if (typeof i.name === 'undefined' || typeof i.value === 'undefined') {
					throw new Error('Configured filter option "entities" contains or returned a value that does not match the expected type.\nValues must correspond to the type { name: string , value: string }');
				}
			});
		}
	});

	export function clear() {
		value = '';
		value1 = value2 = null;
	}

	export function setValue(filter_with_value: FilterWithValue | null) {
		if (!filter_with_value) {
			clear();

			return;
		}

		if (filter_with_value.name !== filter.name) {
			throw new Error('Invalid filter to set values.');
		}

		if (filter.type === FilterType.text) {
			value = filter_with_value.value;
		} else if (filter.type === FilterType.entity) {
			value = filter_with_value.value;
		} else if (filter.type === FilterType.boolean) {
			value = !!filter_with_value.value;
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
	let value: string|boolean = '';

	// For two-value filters, like range or date.
	// They will be concatenated as "value1;value2" in the final value.
	let value1: any = null;
	let value2: any = null;

	function onChange() {
		dispatch('change-filter-value', {filter, value});
	}

	function onChangeBoolean() {
		value = this.checked ? '1' : null;
		onChange();
	}

	function onChangeEntity() {
		let filtered = options.filter((i) => i.value === value1);
		if (filtered.length > 1) {
			throw new Error('More than one element was found in options value.');
		}
		if (filtered.length === 0) {
			throw new Error('No element was found in options value.');
		}
		value = String(value1);
		onChange();
	}

	function parseFilterValueNumber(string: null | string): string {
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
			value = getStringFromDate(value1) + ';' + getStringFromDate(value2);
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
				placeholder={filter.title || filter.name}
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
		{:else if filter.type === FilterType.boolean}
			<div class="form-check">
				<input
					id="input_filter_{filter.name}"
					class="form-check-input"
					type="checkbox"
					placeholder={filter.title || filter.name}
					on:change={onChangeBoolean}
				/>
			</div>
		{:else if filter.type === FilterType.entity}
			<select class="form-select"
				id="input_filter_{filter.name}"
				aria-label="Floating label select example"
				bind:value={value1}
				on:change={onChangeEntity}
			>
				<option selected>-</option>
				{#each options as option}
					<option value="{option.value}">{option.name}</option>
				{/each}
			</select>
		{:else}
			<h5>
				<span class="badge bg-danger">Unknown input type "{filter.type}"</span>
			</h5>
		{/if}
	</div>
</div>

<style lang="scss">
	.filter-row {
		margin-bottom: 1rem;
	}
</style>
