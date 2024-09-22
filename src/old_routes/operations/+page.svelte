<script lang="ts">
	import {
		getOperations,
		getOperationsCount,
		operationsStore,
		updateOperationTags
	} from '$lib/db/operations.ts';
	import PaginatedTable from '$lib/admin/components/PaginatedTable/PaginatedTable.svelte';
	import CollectionField from '$lib/admin/src/CollectionField';
	import Field, { Sortable } from '$lib/admin/src/Field';
	import FieldHtmlProperties from '$lib/admin/src/FieldHtmlProperties';
	import PageHooks from '$lib/admin/src/PageHooks';
	import CallbackAction from '$lib/admin/src/CallbackAction';
	import Operation, { operations_filters } from '$lib/entities/Operation';
	import Modal, { getModal } from '$lib/modal/Modal.svelte';
	import { onMount } from 'svelte';
	import { getTags, getTagsByIds } from '$lib/db/tags';
	import Tag from '$lib/entities/Tag';
	import { success } from '$lib/utils/message';
	import FieldOptions from '$lib/admin/src/FieldOptions';
	import FilterWithValue from '$lib/admin/src/FilterWithValue';
	import UrlAction from '$lib/admin/src/UrlAction';
	import ActionParams from '$lib/admin/src/ActionParams';

	const filters = operations_filters;

	let selected_operation: Operation = null;
	let operationId: number = null;
	let tags: Array<Tag> = [];
	let selected_tags_ids: Array<number> = [];
	let tags_modal: { open: Function; close: Function };

	let fields = [
		new Field('id', 'ID', null, Sortable),
		new Field('date', 'Date', FieldOptions.newWithSortName('operation_date'), Sortable),
		new Field(
			'bank_account',
			'Bank account',
			FieldOptions.newWithAssociatedField(new Field('name'))
		),
		new Field('op_type', 'Type'),
		new Field(
			'details',
			'Details',
			FieldOptions.newWithHtmlProperties({ html_class: 'pre' }),
			Sortable
		),
		new Field(
			'amount_display',
			'Amount',
			new FieldOptions(null, new FieldHtmlProperties('operation-amount'), 'amount_in_cents'),
			Sortable
		),
		new Field('ignored_from_charts', 'Ignored from charts'),
		new CollectionField('tags', 'Tags', new Field('name'))
	];

	let actions = [
		new CallbackAction('Add tags', function (operation: Operation) {
			selected_operation = operation;
			operationId = operation.id;
			selected_tags_ids = operation.tags.map((tag: Tag) => tag.id);

			tags_modal = getModal('tags_modal');
			if (!tags_modal) {
				console.warn('Modal "tags_modal" is not set.');
				return;
			}
			tags_modal.open();
		}),
		new UrlAction('Edit', '/operations/edit/:id', ActionParams.id())
	];

	onMount(async () => {
		tags = await getTags();
	});

	async function saveTags() {
		if (!selected_operation) {
			throw new Error('Operation is not set');
		}
		selected_operation.tags = await getTagsByIds(selected_tags_ids);
		await updateOperationTags(selected_operation);
		success(`Successfully updated tags for operation ${selected_operation.id}!`);
		if (tags_modal) {
			tags_modal.close();
		}
		pageHooks.refresh();
	}

	async function sort(page: number, field: Field, filters: Array<FilterWithValue>) {
		await getOperations(page, field.sortable_field, filters);
	}

	const pageHooks = new PageHooks(getOperations, getOperationsCount);
</script>

<h1>Operations</h1>

<Modal id="tags_modal" title="Add tags to operation with ID {operationId}" clickAction={saveTags}>
	<select
		class="form-select"
		id="tags[]"
		name="tags[]"
		multiple
		size={tags.length > 0 ? 15 : 3}
		bind:value={selected_tags_ids}
	>
		<option value="">- Choose a list of tags -</option>
		{#each tags as tag}
			<option value={tag.id}>{tag.name}</option>
		{/each}
	</select>
</Modal>

<PaginatedTable
	id="operations"
	{actions}
	{fields}
	{filters}
	items_store={operationsStore}
	page_hooks={pageHooks}
	sort_field_callback={sort}
/>
