<script lang="ts">
	import { createTag } from '$lib/db/tags.ts';
	import Tag from '$lib/entities/Tag.ts';
	import { error, success } from '$lib/utils/message.ts';
	import { onMount } from 'svelte';
	import { getTags } from '$lib/db/tags';

	let tags: Tag[] = [];
	let tag: Tag = Tag.empty();
	let submit_button_disabled: boolean = false;

	async function onFormChange() {
		submit_button_disabled = tag.name === '';
	}

	async function submitForm(e: Event) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();

		try {
			await createTag(tag);
		} catch (e) {
			error('An internal error occurred:\n' + e.message);
			console.error(e);
			return;
		}

		success('Tag saved!');
		location.href = '/tags';

		return false;
	}

	onMount(async () => {
		tags = await getTags();
	});
</script>

<form action="#" on:submit={submitForm}>
	<h2>Create new tag rule</h2>

	<div class="row">
		<label for="tag_name" class="col-form-label col-sm-2"> Name </label>
		<div class="col-sm-10">
			<input
				autocomplete=""
				type="text"
				id="tag_name"
				bind:value={tag.name}
				on:change={onFormChange}
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
