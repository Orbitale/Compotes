// @ts-ignore
import Operation, {OperationState} from '$lib/entities/Operation';
import api_call from "$lib/utils/api_call";
import Tag from "$lib/entities/Tag";
import {getTagById, getTagsByIds} from "./tags";
import TagRule from "$lib/entities/TagRule";
import DeserializedTagRule from "./tag_rules";
import {getBankAccountById} from "./bank_accounts";
import BankAccount from "$lib/entities/BankAccount";

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

let operations: Operation[] = [];

export async function getOperations(): Promise<Array<Operation>>
{
    if (!operations.length) {
        let res: string = await api_call("get_operations");

        if (!res) {
            throw 'No results from the API';
        }

        const deserialized_operations: Array<DeserializedOperation> = JSON.parse(res);

        operations = await Promise.all(deserialized_operations.map(async (deserialized_operation: DeserializedOperation) => {
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
        }));
    }

    return Promise.resolve(operations);
}

export function clearOperations() {
    operations = [];
}

export async function getOperationById(id: string): Promise<Operation | null>
{
    if (!operations.length) {
        await getOperations();
    }

    for (const operation of operations) {
        if (operation.id.toString() === id) {
            return operation;
        }
    }

    return null;
}
