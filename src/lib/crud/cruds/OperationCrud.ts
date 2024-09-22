import {
	ArrayField,
	CallbackStateProcessor,
	CallbackStateProvider,
	CheckboxField,
	CrudDefinition,
	DateField,
	KeyValueObjectField,
	List,
	NumberField,
	PaginatedResults,
	TextField,
	UrlAction,
	View,
	type CrudOperation,
	type ListOperationOptions,
	type RequestParameters,
} from '@orbitale/svelte-admin';

import {getOperationById, getOperations, getOperationsCount} from '$lib/db/operations';
import type Operation from '$lib/entities/Operation';
import SortableField from "$lib/admin/src/SortableField";
import type {OrderBy} from "$lib/admin/src/OrderBy";

export default new CrudDefinition<Operation>({
	name: 'operations',
	defaultOperationName: 'list',
	label: { plural: 'Operations', singular: 'Operation' },
	// minStateLoadingTimeMs: 0,

	operations: [
		new List(
			[
				new DateField('operation_date', 'Date', {sortable: true}),
				new TextField('op_type', 'Type 1'),
				new TextField('type_display', 'Type 2'),
				new TextField('details', 'Details'),
				new ArrayField('tags', 'Tags', new KeyValueObjectField('', '', 'name')),
				new NumberField('amount_display', 'Montant')
			],
			[new UrlAction('View', '/crud/operations/view')],
			{
				pagination: {
					enabled: true,
					itemsPerPage: 20
				}
			}
		),
		new View([
			new TextField('id', 'ID'),
			new DateField('operation_date', 'Date'),
			new TextField('op_type', 'Type 1'),
			new TextField('type_display', 'Type 2'),
			new TextField('details', 'Details'),
			new NumberField('amount_in_cents', 'Montant (in cents)'),
			new NumberField('amount', 'Montant'),
			new NumberField('hash', 'Hash'),
			new TextField('state', 'State'),
			new KeyValueObjectField('bank_account', 'Bank account', 'name'),
			new ArrayField('tags', 'Tags', new KeyValueObjectField('', '', 'name')),
			new CheckboxField('ignored_from_charts', 'Is ignored from charts')
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
				const page = Number(requestParameters.page || 1);
				let sort: SortableField|null = null;
				if (requestParameters.sort) {
					const firstKey = Object.keys(requestParameters.sort)[0];
					sort = new SortableField(firstKey, requestParameters.sort[firstKey] as OrderBy, firstKey);
				}
				const results = await getOperations(page, sort);
				const numberOfItems = await getOperationsCount(null);
				return new PaginatedResults(
					page,
					numberOfItems / Number(options.pagination?.itemsPerPage || 10),
					numberOfItems,
					results
				);
			}

			if (operation.name === 'view' || operation.name === 'edit') {
				return getOperationById(Number(requestParameters.id));
			}

			return Promise.resolve(null);
		}
	),
	stateProcessor: new CallbackStateProcessor<Operation>(() => {})
});
