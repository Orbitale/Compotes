<script lang="ts">
	import {
		getTriageOperations,
		deleteOperation,
		triageStore,
		getTriageOperationsCount
	} from '$lib/db/operations';
	import Operation from '$lib/entities/Operation';
	import { success } from '$lib/utils/message';
	import PaginatedTable from '$lib/admin/components/PaginatedTable/PaginatedTable.svelte';
	import ActionParams from '$lib/admin/ActionParams';
	import CallbackAction from '$lib/admin/CallbackAction';
	import Field from '$lib/admin/Field';
	import FieldHtmlProperties from '$lib/admin/FieldHtmlProperties';
	import PageHooks from '$lib/admin/PageHooks';
	import UrlAction from '$lib/admin/UrlAction';
	import FieldOptions from '$lib/admin/FieldOptions';

	let fields = [
		new Field('id', 'ID'),
		new Field('date', 'Date'),
		new Field(
			'bank_account',
			'Bank account',
			FieldOptions.newWithAssociatedField(new Field('name'))
		),
		new Field('op_type', 'Type'),
		new Field('details', 'Details'),
		new Field(
			'amount_display',
			'Amount',
			FieldOptions.newWithHtmlProperties(new FieldHtmlProperties('operation-amount'))
		)
	];

	let actions = [
		new UrlAction('Edit', '/operations/edit/triage-:id', ActionParams.id()),
		new CallbackAction('Delete', doDeleteOperation)
	];

	async function doDeleteOperation(operation: Operation) {
		const id = operation.id;
		if (!(await confirm(`Deleting operation with ID #${operation.id}.\nAre you sure?`))) {
			return;
		}
		await deleteOperation(operation);
		success(`Successfully deleted operation with id ${id}!`);
	}

	const pageHooks = new PageHooks(getTriageOperations, getTriageOperationsCount);
</script>

<h1>Triage</h1>

<PaginatedTable items_store={triageStore} {fields} {actions} page_hooks={pageHooks} />
