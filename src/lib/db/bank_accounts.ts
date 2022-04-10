// @ts-ignore
import BankAccount from '$lib/entities/BankAccount';
import api_call from '$lib/utils/api_call';
import { Writable, writable } from 'svelte/store';

export const bankAccountsStore: Writable<BankAccount[]> = writable();

let bank_accounts: BankAccount[] = [];

export async function getBankAccounts(): Promise<Array<BankAccount>> {
	let res: string = await api_call('bank_account_find_all');
	bank_accounts = JSON.parse(res).map((data: object) => {
		// @ts-ignore
		return new BankAccount(data.id, data.name, data.slug, data.currency);
	});
	bankAccountsStore.set(bank_accounts);

	return bank_accounts;
}

export async function getBankAccountById(id: number): Promise<BankAccount | null> {
	if (!bank_accounts.length) {
		await getBankAccounts();
	}

	for (const bank_account of bank_accounts) {
		if (bank_account.id === id) {
			return bank_account;
		}
	}

	return null;
}

export async function createBankAccount(bank_account: BankAccount): Promise<void> {
	const id = await api_call('bank_account_create', { bankAccount: bank_account.serialize() });

	if (isNaN(+id)) {
		throw new Error('Internal error: API returned a non-number ID.');
	}

	await getBankAccounts();
}

export async function updateBankAccount(bank_account: BankAccount) {
	if (!bank_account.id) {
		throw new Error('Cannot update a bank account that does not have an ID');
	}

	await api_call('bank_account_update', {
		id: bank_account.id,
		name: bank_account.name,
		currency: bank_account.currency
	});

	await getBankAccounts();
}
