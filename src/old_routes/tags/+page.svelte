<script lang="ts">
	import { getTags, tagsStore } from '$lib/db/tags.ts';
	import Tag from '$lib/entities/Tag.ts';
	import PaginatedTable from '$lib/admin/components/PaginatedTable/PaginatedTable.svelte';
	import ActionParams from '$lib/admin/src/ActionParams';
	import Field from '$lib/admin/src/Field';
	import UrlAction from '$lib/admin/src/UrlAction';
	import PageHooks from '$lib/admin/src/PageHooks';

	let tags: Tag[] = [];

	let fields = [new Field('id', 'ID'), new Field('name', 'Date')];

	let actions = [new UrlAction('Edit', '/tags/edit/:id', ActionParams.id())];

	const pageHooks = new PageHooks(getTags);
</script>

<a href="/tags/new" class="btn btn-primary" id="new-button">New</a>

<h1>Tags</h1>

<PaginatedTable id="tags" items_store={tagsStore} {fields} {actions} page_hooks={pageHooks} />

<style lang="scss">
	#new-button {
		float: right;
		margin-top: 8px;
	}
</style>
