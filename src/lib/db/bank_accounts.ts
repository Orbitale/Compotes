// @ts-ignore
import BankAccount from '$lib/entities/BankAccount';
import api_call from '$lib/utils/api_call';
import { writable } from 'svelte/store';
import type { Writable } from 'svelte/store';

export const bankAccountsStore: Writable<BankAccount[]> = writable();

let bank_accounts_promise: Promise<BankAccount[]> | null = null;

async function getBankAccountPromise(): Promise<Array<BankAccount>> {
	if (!bank_accounts_promise) {
		bank_accounts_promise = getBankAccounts();
	}

	return bank_accounts_promise;
}

export async function getBankAccounts(): Promise<Array<BankAccount>> {
	if (bank_accounts_promise) {
		return await bank_accounts_promise;
	}

	let res: string = await api_call('bank_account_find_all');

	const bank_accounts = JSON.parse(res).map((data: object) => {
		// @ts-ignore
		return new BankAccount(data.id, data.name, data.slug, data.currency);
	});

	bankAccountsStore.set(bank_accounts);

	return bank_accounts;
}

export async function getBankAccountsAsChoices(): Promise<Array<{ name: string; value: string }>> {
	const accounts = await getBankAccounts();

	return accounts.map((bankAccount: BankAccount) => {
		return {
			name: bankAccount.name,
			value: bankAccount.id
		};
	});
}

export async function getBankAccountById(id: number): Promise<BankAccount | null> {
	const bank_accounts = await getBankAccountPromise();

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

	bank_accounts_promise = null;
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

	bank_accounts_promise = null;
}
