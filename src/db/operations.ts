// @ts-ignore
import Operation from '../entities/Operation';
import api_fetch from "../utils/api_fetch";

let operations: Operation[] = [];

export async function getOperations(): Promise<Array<Operation>>
{
    if (!operations.length) {
        let res: string = await api_fetch("get_operations");

        console.info("res", res);

        operations = JSON.parse(res).map((data: object) => {
            // @ts-ignore
            return new Operation(
                data.id,
                data.operation_date,
                data.op_type,
                data.type_display,
                data.details,
                data.amount_in_cents,
                data.state,
                data.ignored_from_charts,
                data.bank_account_id,
                data.hash,
            );
        });
    }

    return operations;
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
