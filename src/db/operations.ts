// @ts-ignore
import Operation from '../entities/Operation.ts';
import {invoke} from "@tauri-apps/api/tauri";

let operations: Operation[] = [];

export async function getOperations(): Promise<Array<Operation>>
{
    if (!operations.length) {
        let res: string = await invoke("get_operations");
        operations = JSON.parse(res).map((data: object) => {
            // @ts-ignore
            return new Operation(data.id, data.operation_date, data.op_type, data.type_display, data.details, data.amount_in_cents, data.hash, data.state, data.ignored_from_charts, data.bank_account_id);
        });
    }

    return operations;
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
