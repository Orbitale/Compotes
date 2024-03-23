import {
	CallbackStateProcessor,
	CallbackStateProvider,
	CrudDefinition,
	DateField,
	Delete,
	Edit,
	List,
	NumberField,
	PaginatedResults,
	TextField,
	UrlAction,
	View,
	type CrudOperation,
	type RequestParameters,
	type StateProcessorInput,
	type ListOperationOptions
} from '@orbitale/svelte-admin';

import {
	deleteOperation,
	getOperationById,
	getTriageOperations,
	getTriageOperationsCount,
	updateOperationDetails
} from '$lib/db/operations';
import type Operation from '$lib/entities/Operation';
import { success } from '$lib/utils/message';
import { goto } from '$app/navigation';

export default new CrudDefinition<Operation>({
	name: 'triage',
	defaultOperationName: 'list',
	label: { plural: 'Triaged operations', singular: 'Triaged operation' },
	// minStateLoadingTimeMs: 0,

	operations: [
		new List(
			[
				new DateField('operation_date', 'Date'),
				new TextField('op_type', 'Type 1'),
				new TextField('type_display', 'Type 2'),
				new TextField('details', 'Details'),
				new NumberField('amount_display', 'Montant')
			],
			[
				new UrlAction('View', '/crud/triage/view'),
				new UrlAction('Edit', '/crud/triage/edit'),
				new UrlAction('Delete', '/crud/triage/delete')
			],
			{
				pagination: {
					enabled: true,
					itemsPerPage: 20
				}
			}
		),
		new Delete([], new UrlAction('', '/crud/triage/list')),
		new Edit([new TextField('details', 'Details')]),
		new View([
			new TextField('id', 'ID'),
			new DateField('operation_date', 'Date'),
			new TextField('op_type', 'Type 1'),
			new TextField('type_display', 'Type 2'),
			new TextField('details', 'Details'),
			new NumberField('amount_in_cents', 'Montant (in cents)'),
			new NumberField('amount', 'Montant'),
			new NumberField('hash', 'Hash'),
			new TextField('bank_account', 'Bank account')
		])
	],

	stateProvider: new CallbackStateProvider<Operation>(
		async (operation: CrudOperation, requestParameters: RequestParameters) => {
			if (typeof window === 'undefined') {
				// SSR, can't call Tauri API then.
				return Promise.resolve([]);
			}

			if (operation.name === 'list') {
				const options: ListOperationOptions = operation.options;
				const results = await getTriageOperations(Number(requestParameters.page || 1));
				const numberOfItems = await getTriageOperationsCount();
				return Promise.resolve(
					new PaginatedResults(
						Number(requestParameters.page),
						numberOfItems / Number(options.pagination?.itemsPerPage || 10),
						numberOfItems,
						results
					)
				);
			}

			if (operation.name === 'view' || operation.name === 'edit') {
				return getOperationById(Number(requestParameters.id));
			}

			return Promise.resolve(null);
		}
	),
	stateProcessor: new CallbackStateProcessor<Operation>(
		async (
			data: StateProcessorInput<Operation>,
			operation: CrudOperation,
			requestParameters: RequestParameters
		) => {
			if (operation.name === 'edit' || operation.name === 'delete') {
				if (!data) {
					throw new Error('Cannot create new object: empty data.');
				}
				if (Array.isArray(data)) {
					throw new Error('Cannot update data as array for this action.');
				}

				const operationObject = await getOperationById(Number(requestParameters.id));
				if (!operationObject) {
					throw new Error(`Could not find operation with id "${requestParameters.id}".`);
				}

				if (operation.name === 'edit') {
					operationObject.details = data?.details || '';
					await updateOperationDetails(operationObject);
				}

				if (operation.name === 'delete') {
					await deleteOperation(operationObject);
				}

				success('Success!');
				await goto('/crud/triage/list');
				return;
			}
		}
	)
});
