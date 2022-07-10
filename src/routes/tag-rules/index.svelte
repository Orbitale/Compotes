<script lang="ts">
	import { getTagRules, tagRulesStore } from '$lib/db/tag_rules';
	import PaginatedTable from '$lib/admin/components/PaginatedTable/PaginatedTable.svelte';
	import ActionParams from '$lib/admin/ActionParams';
	import CollectionField from '$lib/admin/CollectionField';
	import Field from '$lib/admin/Field';
	import PageHooks from '$lib/admin/PageHooks';
	import UrlAction from '$lib/admin/UrlAction';

	let tag_rules = [];

	let fields = [
		new Field('id', 'ID'),
		new CollectionField('tags', 'Tags', new Field('name')),
		new Field('is_regex', 'Regular expression'),
		new Field('matching_pattern', 'Matching pattern')
	];

	let actions = [new UrlAction('Edit', '/tag-rules/edit/:id', ActionParams.id())];

	const pageHooks = new PageHooks(getTagRules);
</script>

<a href="/tag-rules/new" class="btn btn-primary" id="new-button">New</a>

<h1>Tag rules</h1>

<PaginatedTable
	id="tag_rule"
	items_store={tagRulesStore}
	{fields}
	{actions}
	page_hooks={pageHooks}
/>

<style lang="scss">
	#new-button {
		float: right;
		margin-top: 8px;
	}
</style>
