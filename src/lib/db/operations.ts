import Operation, {OperationState} from '$lib/entities/Operation';
import api_call from "$lib/utils/api_call";
import {getTagsByIds} from "./tags";
import {getBankAccountById} from "./bank_accounts";
import {writable} from "svelte/store";

export const operationsStore = writable();
export const triageStore = writable();

let operations: Operation[] = [];
let triage: Operation[] = [];

export default class DeserializedOperation
{
    public readonly id!: number;
    public readonly operation_date!: string;
    public readonly op_type!: string;
    public readonly type_display!: string;
    public readonly details!: string;
    public readonly amount_in_cents!: number;
    public readonly hash!: string;
    public readonly state!: OperationState;
    public readonly ignored_from_charts!: boolean;
    public readonly bank_account_id!: number;
    public readonly tags_ids!: Array<number>;
}

export async function getOperations(): Promise<Array<Operation>>
{
    let res: string = await api_call("operations_get");

    if (!res) {
        throw 'No results from the API';
    }

    operations = await deserializeAndNormalizeDatabaseResult(res);

    operationsStore.set(operations);

    return operations;
}

export async function getTriageOperations(): Promise<Array<Operation>>
{
    let res: string = await api_call("operations_get_triage");

    if (!res) {
        throw 'No results from the API';
    }

    const operations = await deserializeAndNormalizeDatabaseResult(res);

    triage = operations;
    triageStore.set(operations);

    return operations;
}

export async function updateOperationDetails(operation: Operation)
{
    await api_call("operation_update_details", {id: operation.id.toString(), details: operation.details});
}

export async function deleteOperation(operation: Operation)
{
    const id = operation.id.toString(10);

    await api_call("operation_delete", {id: id});

    const newOps = operations.filter((op: Operation) => op.id.toString(10) !== id);
    operations = newOps;
    operationsStore.set(newOps);

    const newTriage = triage.filter((op: Operation) => op.id.toString(10) !== id);
    triage = newTriage;
    triageStore.set(newTriage);
}

export async function getOperationById(id: number): Promise<Operation | null>
{
    let res: string = await api_call("operations_get_by_id", {id: id.toString()});

    if (!res) {
        throw 'No results from the API';
    }

    const deserialized_operation: DeserializedOperation = JSON.parse(res);

    return normalizeOperationFromDeserialized(deserialized_operation);
}

async function deserializeAndNormalizeDatabaseResult(res: string): Promise<Array<Operation>> {
    const deserialized_operations: Array<DeserializedOperation> = JSON.parse(res);

    return await Promise
        .all(
            deserialized_operations.map(
                async (deserialized_operation: DeserializedOperation) => await normalizeOperationFromDeserialized(deserialized_operation)
            )
        )
    ;
}

async function normalizeOperationFromDeserialized(deserialized_operation: DeserializedOperation): Promise<Operation>
{
    return new Operation(
        deserialized_operation.id,
        deserialized_operation.operation_date,
        deserialized_operation.op_type,
        deserialized_operation.type_display,
        deserialized_operation.details,
        deserialized_operation.amount_in_cents,
        deserialized_operation.state,
        deserialized_operation.ignored_from_charts,
        await getBankAccountById(deserialized_operation.bank_account_id.toString()),
        deserialized_operation.hash,
        await getTagsByIds(deserialized_operation.tags_ids)
    );
}
