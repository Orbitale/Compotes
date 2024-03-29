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
	import ActionParams from '$lib/admin/src/ActionParams';
	import CallbackAction from '$lib/admin/src/CallbackAction';
	import Field from '$lib/admin/src/Field';
	import FieldHtmlProperties from '$lib/admin/src/FieldHtmlProperties';
	import PageHooks from '$lib/admin/src/PageHooks';
	import UrlAction from '$lib/admin/src/UrlAction';
	import FieldOptions from '$lib/admin/src/FieldOptions';

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

<PaginatedTable id="triage" items_store={triageStore} {fields} {actions} page_hooks={pageHooks} />
