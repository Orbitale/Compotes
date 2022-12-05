<script lang="ts">
	import { getTagById, updateTag } from '$lib/db/tags.ts';
	import Tag from '$lib/entities/Tag.ts';
	import { onMount } from 'svelte';
	import { error, success } from '$lib/utils/message.ts';
	import { page } from '$app/stores';

	let id: string = $page.params.id;

	let tag: Tag = Tag.empty();
	let submit_button_disabled: boolean = false;

	onMount(async () => {
		const fetched_tag = await getTagById(parseInt(id, 10));
		if (!fetched_tag) {
			throw `Tag with ID "${id}" does not exist.`;
		}
		tag = fetched_tag.clone();
	});

	function onNameChange() {
		submit_button_disabled = !tag.name;
	}

	async function submitForm(e: Event) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();

		try {
			await updateTag(tag);
		} catch (e) {
			error(e);
			return;
		}

		success('Tag saved!');

		location.href = '/tags';

		return false;
	}
</script>

{#if !tag}
	<div class="alert alert-danger">Invalid or inexistent tag.</div>
{:else}
	<form action="#" on:submit={submitForm}>
		<h2>Edit tag</h2>

		<div class="row">
			<label for="name" class="col-form-label col-sm-2"> Name </label>
			<div class="col-sm-10">
				<input
					autocomplete=""
					type="text"
					id="name"
					bind:value={tag.name}
					on:keyup={onNameChange}
					class="form-control"
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
