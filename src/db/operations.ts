// @ts-ignore
import Operation from '../entities/Operation.ts';
import {invoke} from "@tauri-apps/api/tauri";

let operations: Operation[] = [];

export async function getOperations()
{
    if (!operations.length) {
        let res: string = await invoke("get_operations");
        operations = JSON.parse(res);
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
