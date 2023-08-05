<script lang="ts">
	import {
		getOperationById,
		ignoreOperationFromAnalytics,
		updateOperationTags
	} from '$lib/db/operations.ts';
	import Operation from '$lib/entities/Operation.ts';
	import { onMount } from 'svelte';
	import { error, success } from '$lib/utils/message.ts';
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import Tag from '$lib/entities/Tag';
	import { getTags } from '$lib/db/tags';

	let id: string;

	$: id = $page.params.id;

	let tags: Tag[] = [];
	let operation_tags: number[] = [];
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
		operation_tags = operation.tags.map((tag: Tag) => tag.id);
		tags = await getTags();
	});

	async function onFormChange() {
		submit_button_disabled = false;

		operation_tags = operation_tags.filter((tagId) => tagId > 0);

		if (operation_tags.length) {
			for (let normalizedTag of operation_tags) {
				if (isNaN(normalizedTag)) {
					error('Invalid tag ID in list. Please re-check.');
					return;
				}
			}
		}

		operation.tags = operation_tags.map((tagId) => {
			const matchingTag = tags.filter((internalTag) => internalTag.id === tagId);
			if (matchingTag.length !== 1) {
				throw new Error(`Invalid tag id ${tagId}. Not found in memory.`);
			}
			return matchingTag[0];
		});
	}

	async function submitForm(e: Event) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();

		try {
			await updateOperationTags(operation);
			await ignoreOperationFromAnalytics(operation);
		} catch (e) {
			error(e.message || e);
			return;
		}

		success('Operation updated!');

		await goto('/operations');

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
			<div class="col-sm-2">
				<label for="tags[]">Tags</label>
			</div>
			<div class="col-sm-10">
				<select
					class="form-select"
					name="tags[]"
					multiple
					size={tags.length > 0 ? 15 : 3}
					bind:value={operation_tags}
					on:change={onFormChange}
				>
					<option value="">- Choose a list of tags -</option>
					{#each tags as tag}
						<option value={tag.id}>{tag.name}</option>
					{/each}
				</select>
			</div>
		</div>

		<div class="row">
			<div class="offset-sm-2 col-sm-10">
				<div class="form-check">
					<input
						class="form-check-input"
						type="checkbox"
						id="ignored_from_charts"
						bind:checked={operation.ignored_from_charts}
						on:change={onFormChange}
					/>
					<label class="form-check-label" for="ignored_from_charts">
						Ignore this operation in analytics
					</label>
				</div>
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
