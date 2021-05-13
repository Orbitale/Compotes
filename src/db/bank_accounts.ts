// @ts-ignore
import BankAccount from '../entities/BankAccount.ts';
import {invoke} from "@tauri-apps/api/tauri";

let bank_accounts: BankAccount[] = [];

export async function getBankAccounts(): Promise<Array<BankAccount>>
{
    if (!bank_accounts.length) {
        let res: string = await invoke("get_bank_accounts");
        bank_accounts = JSON.parse(res);
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
