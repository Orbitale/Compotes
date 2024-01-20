import {
    CallbackStateProcessor,
    CallbackStateProvider,
    CrudDefinition, Edit,
    List,
    TextField,
    UrlAction,
} from "@orbitale/svelte-admin";
import type {CrudBankAccount} from "@orbitale/svelte-admin/dist/Crud/BankAccounts";
import type {RequestParameters} from "@orbitale/svelte-admin/dist/request";
import {getBankAccountById, getBankAccounts} from "$lib/db/bank_accounts";
import type BankAccount from "$lib/entities/BankAccount";

const baseFields = [
    new TextField('name', 'Name'),
    new TextField('slug', 'Identifier'),
    new TextField('currency', 'Currency'),
];

export default new CrudDefinition<BankAccount>('bank-accounts', {
    defaultOperationName: "list",
    label: {plural: "BankAccounts", singular: "BankAccount"},
    // minStateLoadingTimeMs: 0,

    operations: [
        new List([...baseFields],
            [
                new UrlAction('Edit', '/crud/bank-accounts/edit'),
            ]),
        new Edit(baseFields),
    ],

    stateProvider: new CallbackStateProvider<BankAccount>(async (bankAccount: CrudBankAccount, requestParameters: RequestParameters) => {
        if (typeof window === 'undefined') {
            // SSR, can't call Tauri API then.
            return Promise.resolve([]);
        }

        if (bankAccount.name === 'list') {
            const results = await getBankAccounts(requestParameters.page||1);
            return Promise.resolve(results);
        }

        if (bankAccount.name === 'view' || bankAccount.name === 'edit') {
            return getBankAccountById(requestParameters.id);
        }

        return Promise.resolve(null);
    }),
    stateProcessor: new CallbackStateProcessor<BankAccount>(() => {})
});
