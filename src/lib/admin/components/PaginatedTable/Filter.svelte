

<script lang="ts">
	import ConfigFilter from '$lib/admin/ConfigFilter';
	import FilterType from '$lib/admin/FilterType';
	import DatePicker from '@beyonk/svelte-datepicker/src/components/DatePicker.svelte';
	import { dayjs } from '@beyonk/svelte-datepicker/src/components/lib/date-utils';

	export let filter: ConfigFilter;
	export let change_callback: Function;

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

	function getDateFromEvent(event: { detail: { date: dayjs } }): string {
		return dayjs(event.detail.date).format('YYYY-MM-DD');
	}

	function updateValueFromValues() {
		if (filter.type === FilterType.date) {
			value1 = value1 ? value1 : dayjs().substract(50, 'year').format('YYYY-MM-DD');
			value2 = value2 ? value2 : dayjs().add(1, 'day').format('YYYY-MM-DD');
		}

		value = `${value1};${value2}`;
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
				selected={false}
				placeholder="After date"
				format="YYYY/MM/DD"
				on:date-selected={(e) => {
					value1 = getDateFromEvent(e);
					updateValueFromValues();
				}}
				on:change={onChange}
			/>
			<DatePicker
				start={dayjs().subtract(50, 'year')}
				end={dayjs().add(1, 'year')}
				selected={false}
				placeholder="Before date"
				format="YYYY/MM/DD"
				on:date-selected={(e) => {
					value1 = getDateFromEvent(e);
					updateValueFromValues();
				}}
				on:change={onChange}
			/>
		{:else}
			<div class="badge">Unknown input type "{filter.type}"</div>
		{/if}
	</div>
</div>
