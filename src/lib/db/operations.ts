import Operation, { OperationState } from '$lib/entities/Operation';
import api_call from '$lib/utils/api_call';
import { getTagsByIds } from './tags';
import { getBankAccountById } from './bank_accounts';
import { writable } from 'svelte/store';
import type { Writable } from 'svelte/store';
import type Tag from '$lib/entities/Tag';
import type SortableField from '$lib/admin/SortableField';
import { OrderBy, orderByToString } from '$lib/admin/OrderBy';
import type FilterWithValue from '$lib/admin/FilterWithValue';

export const operationsStore: Writable<Operation[]> = writable();
export const triageStore: Writable<Operation[]> = writable();

const lastTriageCall = {
	page: 1
};

export default class DeserializedOperation {
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

export async function getOperations(
	page: number,
	sortableField: SortableField | null = null,
	filters: Array<FilterWithValue> | null = null
): Promise<Array<Operation>> {
	if (!page) {
		page = 1;
	}

	const params = { page };
	if (sortableField) {
		params['orderField'] = sortableField.property_name;
		params['orderBy'] = orderByToString(sortableField.order_by || OrderBy.DESC);
	}

	if (filters && filters.length) {
		params['filters'] = filters;
	}

	const res: string = await api_call('operations_get', params);

	if (!res) {
		throw 'No results from the API';
	}

	const new_items = await deserializeAndNormalizeDatabaseResult(res);

	operationsStore.set(new_items);

	return new_items;
}

export async function getOperationsCount(filters: Array<FilterWithValue> | null): Promise<number> {
	const res: string = await api_call('operations_get_count', {filters});

	return normalizeCountFromApiResult(res);
}

export async function getTriageOperations(page: number): Promise<Array<Operation>> {
	const res: string = await api_call('operations_get_triage', { page: page });

	if (!res) {
		throw 'No results from the API';
	}

	const triage = await deserializeAndNormalizeDatabaseResult(res);

	triageStore.set(triage);

	lastTriageCall.page = page;

	return triage;
}

export async function getTriageOperationsCount(): Promise<number> {
	const res: string = await api_call('operations_get_triage_count');

	return normalizeCountFromApiResult(res);
}

export async function updateOperationDetails(operation: Operation) {
	await api_call('operation_update_details', {
		id: operation.id.toString(),
		details: operation.details
	});
}

export async function updateOperationTags(operation: Operation) {
	await api_call('operation_update_tags', {
		id: operation.id.toString(),
		tags: operation.tags.map((tag: Tag) => tag.id)
	});
}

export async function deleteOperation(operation: Operation) {
	const id = operation.id.toString(10);

	await api_call('operation_delete', { id: id });

	await getTriageOperations(lastTriageCall.page);
}

export async function getOperationById(id: number): Promise<Operation | null> {
	const res: string = await api_call('operations_get_by_id', { id: id.toString() });

	if (!res) {
		throw 'No results from the API';
	}

	const deserialized_operation: DeserializedOperation = JSON.parse(res);

	return normalizeOperationFromDeserialized(deserialized_operation);
}

async function deserializeAndNormalizeDatabaseResult(res: string): Promise<Array<Operation>> {
	const deserialized_operations: Array<DeserializedOperation> = JSON.parse(res);

	return await Promise.all(
		deserialized_operations.map(
			async (deserialized_operation: DeserializedOperation) =>
				await normalizeOperationFromDeserialized(deserialized_operation)
		)
	);
}

async function normalizeOperationFromDeserialized(
	deserialized_operation: DeserializedOperation
): Promise<Operation> {
	return new Operation(
		deserialized_operation.id,
		deserialized_operation.operation_date,
		deserialized_operation.op_type,
		deserialized_operation.type_display,
		deserialized_operation.details,
		deserialized_operation.amount_in_cents,
		deserialized_operation.state,
		deserialized_operation.ignored_from_charts,
		await getBankAccountById(deserialized_operation.bank_account_id),
		deserialized_operation.hash,
		await getTagsByIds(deserialized_operation.tags_ids)
	);
}

function normalizeCountFromApiResult(res: string): number {
	if (res === '') {
		throw 'No results from the API';
	}

	const count = parseInt(res, 10);

	if (isNaN(count)) {
		throw 'Invalid results from the API';
	}

	return count;
}
