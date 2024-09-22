import BankAccount from '$lib/entities/BankAccount';
import api_call from '$lib/utils/api_call';

const cache: Record<string, BankAccount> = {};

export async function getBankAccounts(): Promise<Array<BankAccount>> {
	const res: string = await api_call('bank_account_find_all');

	const bank_accounts: Array<BankAccount> = JSON.parse(res).map((data: BankAccount) => {
		return new BankAccount(data.id, data.name, data.slug, data.currency);
	});
	bank_accounts.forEach(a => cache[a.id] = a);

	return bank_accounts;
}

export async function getBankAccountsAsChoices(): Promise<Array<{ name: string; value: string }>> {
	const accounts = await getBankAccounts();

	return accounts.map((bankAccount: BankAccount) => {
		return {
			name: bankAccount.name,
			value: bankAccount.id.toString()
		};
	});
}

export async function getBankAccountById(id: number): Promise<BankAccount | null> {
	if (cache[id]) {
		return Promise.resolve(cache[id]);
	}

	const res: string = await api_call('bank_account_get_by_id', { id: id.toString() });

	if (!res) {
		throw 'No results from the API';
	}

	const bank_account: BankAccount = JSON.parse(res);

	if (!bank_account) {
		throw new Error('Could not deserialize bank account.');
	}

	cache[id] = bank_account;

	return bank_account;
}

export async function createBankAccount(bank_account: BankAccount): Promise<void> {
	const id = await api_call('bank_account_create', { bankAccount: bank_account.serialize() });

	if (isNaN(+id)) {
		throw new Error('Internal error: API returned a non-number ID.');
	}

	cache[id] = bank_account;
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

	cache[bank_account.id] = bank_account;
}
