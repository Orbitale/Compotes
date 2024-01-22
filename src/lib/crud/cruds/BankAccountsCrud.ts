import {
    CallbackStateProcessor,
    CallbackStateProvider,
    CrudDefinition, Edit,
    List,
    TextField,
    UrlAction,
} from "@orbitale/svelte-admin";
import type {RequestParameters} from "@orbitale/svelte-admin/dist/request";
import type {CrudOperation} from "@orbitale/svelte-admin/dist/Crud/Operations";
import type {StateProcessorInput} from "@orbitale/svelte-admin/dist/State/Processor";

import {createBankAccount, getBankAccountById, getBankAccounts, updateBankAccount} from "$lib/db/bank_accounts";
import type BankAccount from "$lib/entities/BankAccount";
import {goto} from "$app/navigation";
import {success} from "$lib/utils/message";

const baseFields = [
    new TextField('name', 'Name'),
    new TextField('slug', 'Identifier', {disabled: true}),
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

    stateProvider: new CallbackStateProvider<BankAccount>(async (operation: CrudBankAccount, requestParameters: RequestParameters) => {
        if (typeof window === 'undefined') {
            // SSR, can't call Tauri API then.
            return Promise.resolve([]);
        }

        if (operation.name === 'list') {
            return getBankAccounts();
        }

        if (operation.name === 'view' || operation.name === 'edit') {
            return getBankAccountById(requestParameters.id);
        }

        return Promise.resolve(null);
    }),

    stateProcessor: new CallbackStateProcessor<BankAccount>(async (data: StateProcessorInput<BankAccount>, operation: CrudOperation, requestParameters: RequestParameters) => {
        if (operation.name === 'new') {
            return createBankAccount(data);
        }

        if (operation.name === 'edit') {
            data.id = parseInt(requestParameters.id, 10);
            await updateBankAccount(data);
            success('Success!');
            await goto('/crud/bank-accounts/list');
            return;
        }
    })
});
