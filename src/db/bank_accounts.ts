// @ts-ignore
import BankAccount from '../entities/BankAccount';
import api_call from "../utils/api_call";

let bank_accounts: BankAccount[] = [];

export async function getBankAccounts(): Promise<Array<BankAccount>>
{
    if (!bank_accounts.length) {
        let res: string = await api_call("get_bank_accounts");
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

export async function saveBankAccount(bank_account: BankAccount): Promise<void>
{
    await api_call("save_bank_account", {bankAccount: bank_account.serialize()});

    if (bank_account.id) {
        const bank_account_entity = await getBankAccountById(bank_account.id.toString());

        if (!bank_account_entity) throw new Error('Data corruption detected in bank account.');

        bank_account_entity.mergeWith(bank_account);
    } else {
        bank_accounts = []; // Reset to reload again afterwards
    }
}
