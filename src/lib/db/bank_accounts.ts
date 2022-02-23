// @ts-ignore
import BankAccount from '$lib/entities/BankAccount';
import api_call from "$lib/utils/api_call";
import {writable} from "svelte/store";

export const bankAccountsStore = writable();

let bank_accounts: BankAccount[] = [];

export async function getBankAccounts(): Promise<Array<BankAccount>>
{
    if (!bank_accounts.length) {
        let res: string = await api_call("get_bank_accounts");
        bank_accounts = JSON.parse(res).map((data: object) => {
            // @ts-ignore
            return new BankAccount(data.id, data.name, data.slug, data.currency);
        });
        bankAccountsStore.set(bank_accounts);
    }

    return bank_accounts;
}

export async function getBankAccountById(id: string): Promise<BankAccount | null>
{
    if (!bank_accounts.length) {
        await getBankAccounts();
    }

    for (const bank_account of bank_accounts) {
        if (bank_account.id.toString() === id) {
            return bank_account;
        }
    }

    return null;
}

export async function createBankAccount(bank_account: BankAccount): Promise<void>
{
    const id = await api_call("save_bank_account", {bankAccount: bank_account.serialize()});

    if (isNaN(+id)) {
        throw new Error('Internal error: API returned a non-number ID.');
    }

    bank_account.setId(+id);
}
