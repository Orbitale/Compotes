<script lang="ts">
	import { getTagRuleById, updateTagRule } from '$lib/db/tag_rules.ts';
	import TagRule from '$lib/entities/TagRule.ts';
	import { error, success } from '$lib/utils/message.ts';
	import Tag from '$lib/entities/Tag';
	import { page } from '$app/stores';
	import { onMount } from 'svelte';
	import { getTags } from '$lib/db/tags';
	import { goto } from "$app/navigation";

	let id: string = $page.params.id;

	let tags: Tag[] = [];
	let tag_rule_tags: number[] = [];
	let tag_rule: TagRule = TagRule.empty();
	let submit_button_disabled: boolean = false;

	async function onFormChange() {
		submit_button_disabled = tag_rule.tags.length === 0 || tag_rule.matching_pattern.length === 0;

		tag_rule_tags = tag_rule_tags.filter((tagId) => tagId > 0);

		if (tag_rule_tags.length) {
			for (let normalizedTag of tag_rule_tags) {
				if (isNaN(normalizedTag)) {
					error('Invalid tag ID in list. Please re-check.');
					return;
				}
			}
		}

		tag_rule.tags = tag_rule_tags.map((tagId) => {
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
			await updateTagRule(tag_rule);
		} catch (e) {
			error('An internal error occurred:\n' + e.message);
			console.error(e);
			return;
		}

		success('Tag rule saved!');

		await goto('/tag-rules');

		return false;
	}

	onMount(async () => {
		tag_rule = await getTagRuleById(id);
		tag_rule_tags = tag_rule.tags.map((tag: Tag) => tag.id);
		tags = await getTags();
	});
</script>

<form action="#" on:submit={submitForm}>
	<h2>Update a tag rule</h2>

	<div class="row">
		<label for="matching_pattern" class="col-form-label col-sm-2"> Matching pattern </label>
		<div class="col-sm-10">
			<input
				autocomplete=""
				type="text"
				id="matching_pattern"
				bind:value={tag_rule.matching_pattern}
				on:change={onFormChange}
				class="form-control"
			/>
		</div>
	</div>

	<div class="row">
		<div class="offset-sm-2 col-sm-10">
			<div class="form-check">
				<input
					class="form-check-input"
					type="checkbox"
					id="is_regex"
					bind:checked={tag_rule.is_regex}
					on:change={onFormChange}
				/>
				<label class="form-check-label" for="is_regex"> Pattern is a regular expression </label>
			</div>
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
				bind:value={tag_rule_tags}
				on:change={onFormChange}
			>
				<option value="">- Choose a list of tags -</option>
				{#each tags as tag}
					<option value={tag.id}>{tag.name}</option>
				{/each}
			</select>
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

<style lang="scss">
	.form-check {
		margin-top: 1em;
		margin-bottom: 1em;
	}
</style>
