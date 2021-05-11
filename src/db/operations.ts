// @ts-ignore
import Operation from '../entities/Operation.ts';
import {invoke} from "@tauri-apps/api/tauri";

let operations = [];

export async function getOperations()
{
    if (!operations.length) {
        let res: string = await invoke("get_operations");
        operations = JSON.parse(res);
    }

    return operations;
}

export function getOperationById(id: string): Operation | null
{
    for (const operation of operations) {
        if (operation.id === id) {
            return operation;
        }
    }

    return null;
}
