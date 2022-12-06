<script lang="ts">
	import { getOperationById, updateOperationDetails } from '$lib/db/operations.ts';
	import Operation from '$lib/entities/Operation.ts';
	import { onMount } from 'svelte';
	import { error, success } from '$lib/utils/message.ts';
	import { page } from '$app/stores';
	import { goto } from "$app/navigation";

	let id: string;

	$: id = $page.params.id;

	let previous_details: String;
	let operation: Operation | null = null;
	let submit_button_disabled: boolean = true;

	onMount(async () => {
		const fetched_operation = await getOperationById(parseInt(id, 10));
		if (!fetched_operation) {
			throw `Operation with ID "${id}" does not exist.`;
		}
		operation = fetched_operation;
		previous_details = operation.details.trim();
	});

	function onDetailsChange() {
		operation.details = operation.details.trim();
		submit_button_disabled = operation.details === previous_details;
	}

	async function submitForm(e: Event) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();

		try {
			await updateOperationDetails(operation);
		} catch (e) {
			error(e.message || e);
			return;
		}

		success('Operation updated!');

		await goto('/triage');

		return false;
	}
</script>

{#if !operation}
	<div class="alert alert-danger">Invalid or inexistent operation.</div>
{:else}
	<form action="#" on:submit={submitForm}>
		<h2>Edit operation</h2>

		<div class="row">
			<label for="previous_details" class="col-form-label col-sm-2"> Previous details </label>
			<div class="col-sm-10">
				<input
					disabled
					readonly
					type="text"
					id="previous_details"
					value={previous_details}
					class="form-control"
				/>
			</div>
		</div>

		<div class="row">
			<label for="details" class="col-form-label col-sm-2"> New details </label>
			<div class="col-sm-10">
				<input
					autocomplete=""
					type="text"
					id="details"
					bind:value={operation.details}
					class="form-control"
					on:change={onDetailsChange}
				/>
			</div>
		</div>

		<br />

		<div class="row">
			<div class="col-sm-2">&nbsp;</div>
			<div class="col-sm-10">
				<button
					type="submit"
					class="btn btn-primary {submit_button_disabled ? 'disabled' : ''}"
					disabled={submit_button_disabled}
				>
					Save
				</button>
			</div>
		</div>
	</form>
{/if}
