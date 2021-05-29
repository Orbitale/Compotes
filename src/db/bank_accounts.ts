// @ts-ignore
import BankAccount from '../entities/BankAccount.ts';
import api_fetch from "../utils/api_fetch.ts";

let bank_accounts: BankAccount[] = [];

export async function getBankAccounts(): Promise<Array<BankAccount>>
{
    if (!bank_accounts.length) {
        let res: string = await api_fetch("get_bank_accounts");
        bank_accounts = JSON.parse(res).map((data: object) => {
            // @ts-ignore
            return new BankAccount(data.id, data.name, data.slug, data.currency);
        });
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
