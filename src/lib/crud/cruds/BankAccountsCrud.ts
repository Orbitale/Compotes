import {
	CallbackStateProcessor,
	CallbackStateProvider,
	CrudDefinition,
	Edit,
	List,
	New,
	TextField,
	UrlAction,
	type RequestParameters,
	type CrudOperation,
	type StateProcessorInput
} from '@orbitale/svelte-admin';

import {
	createBankAccount,
	getBankAccountById,
	getBankAccounts,
	updateBankAccount
} from '$lib/db/bank_accounts';
import BankAccount from '$lib/entities/BankAccount';
import { goto } from '$app/navigation';
import { success } from '$lib/utils/message';

const baseFields = [
	new TextField('name', 'Name'),
	new TextField('slug', 'Identifier', { disabled: true }),
	new TextField('currency', 'Currency')
];

export default new CrudDefinition<BankAccount>({
	name: 'bank-accounts',
	defaultOperationName: 'list',
	label: { plural: 'BankAccounts', singular: 'BankAccount' },
	// minStateLoadingTimeMs: 0,

	operations: [
		new List([...baseFields], [new UrlAction('Edit', '/crud/bank-accounts/edit')], {
			globalActions: [new UrlAction('New', '/crud/bank-accounts/new')]
		}),
		new Edit(baseFields),
		new New(baseFields),
	],

	stateProvider: new CallbackStateProvider<BankAccount>(
		async (operation: CrudOperation, requestParameters: RequestParameters) => {
			if (typeof window === 'undefined') {
				// SSR, can't call Tauri API then.
				return Promise.resolve([]);
			}

			if (operation.name === 'list') {
				return getBankAccounts();
			}

			if (operation.name === 'view' || operation.name === 'edit') {
				return getBankAccountById(Number(requestParameters.id));
			}

			return Promise.resolve(null);
		}
	),

	stateProcessor: new CallbackStateProcessor<BankAccount>(
		async (
			data: StateProcessorInput<BankAccount>,
			operation: CrudOperation,
			requestParameters: RequestParameters
		) => {
			if (operation.name === 'new') {
				if (!data) {
					throw new Error('Cannot create new object: empty data.');
				}

				await createBankAccount(BankAccount.fromObject(data));
				success('Success!');
				await goto('/crud/bank-accounts/list');
			}

			if (operation.name === 'edit') {
				if (!data) {
					throw new Error('Cannot create new object: empty data.');
				}
				if (Array.isArray(data)) {
					throw new Error('Cannot update data as array for this action.');
				}

				data.id = Number(requestParameters.id);

				await updateBankAccount(data);
				success('Success!');
				await goto('/crud/bank-accounts/list');

				return;
			}
		}
	)
});
