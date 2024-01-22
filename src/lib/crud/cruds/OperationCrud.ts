import {
    CallbackStateProcessor,
    CallbackStateProvider, CheckboxField,
    CrudDefinition, DateField,
    List,
    NumberField,
    PaginatedResults,
    TextField,
    UrlAction,
    View
} from "@orbitale/svelte-admin";
import type {CrudOperation} from "@orbitale/svelte-admin/dist/Crud/Operations";
import type {RequestParameters} from "@orbitale/svelte-admin/dist/request";

import {getOperationById, getOperations, getOperationsCount} from "$lib/db/operations";
import type Operation from "$lib/entities/Operation";

export default new CrudDefinition<Operation>('operations', {
    defaultOperationName: "list",
    label: {plural: "Operations", singular: "Operation"},
    // minStateLoadingTimeMs: 0,

    operations: [
        new List(
            [
                new DateField('operation_date', 'Date'),
                new TextField('op_type', 'Type 1'),
                new TextField('type_display', 'Type 2'),
                new TextField('details', 'Details'),
                new TextField('tags', 'Tags'),
                new NumberField('amount_display', 'Montant'),
            ],
            [
                new UrlAction('View', '/crud/operations/view'),
            ],
            {
            pagination: {
                enabled: true,
                itemsPerPage: 20,
            }
        }),
        new View([
            new TextField('id', 'ID'),
            new DateField('operation_date', 'Date'),
            new TextField('op_type', 'Type 1'),
            new TextField('type_display', 'Type 2'),
            new TextField('details', 'Details'),
            new NumberField('amount_in_cents', 'Montant (in cents)'),
            new NumberField('amount', 'Montant'),
            new NumberField('hash', 'Hash'),
            new TextField('state', 'State'),
            new TextField('bank_account', 'Bank account'),
            new TextField('tags', 'Tags'),
            new CheckboxField('ignored_from_charts', 'Is ignored from charts'),
        ]),
    ],

    stateProvider: new CallbackStateProvider<Operation>(async (operation: CrudOperation, requestParameters: RequestParameters) => {
        if (typeof window === 'undefined') {
            // SSR, can't call Tauri API then.
            return Promise.resolve([]);
        }

        if (operation.name === 'list') {
            const results = await getOperations(requestParameters.page||1);
            const numberOfItems = await getOperationsCount(null);
            return Promise.resolve(new PaginatedResults(requestParameters.page, numberOfItems / operation.options.pagination.itemsPerPage, numberOfItems, results));
        }

        if (operation.name === 'view' || operation.name === 'edit') {
            return getOperationById(requestParameters.id);
        }

        return Promise.resolve(null);
    }),
    stateProcessor: new CallbackStateProcessor<Operation>(() => {})
});
