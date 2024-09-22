import Operation, { OperationState } from '$lib/entities/Operation';
import api_call from '$lib/utils/api_call';
import { getTagsByIds } from './tags';
import { getBankAccountById } from './bank_accounts';
import type Tag from '$lib/entities/Tag';
import type SortableField from '$lib/admin/src/SortableField';
import { OrderBy, orderByToString } from '$lib/admin/src/OrderBy';
import type FilterWithValue from '$lib/admin/src/FilterWithValue';
import type SavedFilter from '$lib/admin/src/SavedFilter';

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
	page: number = 1,
	sortableField: SortableField | null = null,
	filters: Array<FilterWithValue> | null = null
): Promise<Array<Operation>> {
	const params: { [key: string]: number | string | Array<FilterWithValue> } = { page };

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

	return await deserializeAndNormalizeDatabaseResult(res);
}

export async function getOperationsForAnalytics(
	saved_filter: SavedFilter
): Promise<Array<Operation>> {
	const params = {
		filters: saved_filter.deserialized_filters
	};

	const res: string = await api_call('operations_get_analytics', params);

	if (!res) {
		throw 'No results from the API';
	}

	return await deserializeAndNormalizeDatabaseResult(res);
}

export async function getOperationsCount(filters: Array<FilterWithValue> | null): Promise<number> {
	const res: string = await api_call('operations_get_count', { filters });

	return normalizeCountFromApiResult(res);
}

export async function getTriageOperations(page: number = 1): Promise<Array<Operation>> {
	const res: string = await api_call('operations_get_triage', { page: page });

	if (!res) {
		throw 'No results from the API';
	}

	return await deserializeAndNormalizeDatabaseResult(res);
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

export async function ignoreOperationFromAnalytics(operation: Operation) {
	await api_call('operation_update_ignore_from_analytics', {
		id: operation.id.toString(),
		ignoredFromCharts: operation.ignored_from_charts
	});
}

export async function deleteOperation(operation: Operation) {
	const id = operation.id.toString(10);

	await api_call('operation_delete', { id: id });
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
	const bank_account = await getBankAccountById(deserialized_operation.bank_account_id);

	if (!bank_account) {
		throw new Error(
			`Backend could not find bank account with id "${deserialized_operation.bank_account_id}".`
		);
	}

	const tags = await getTagsByIds(deserialized_operation.tags_ids);

	return new Operation(
		deserialized_operation.id,
		deserialized_operation.operation_date,
		deserialized_operation.op_type,
		deserialized_operation.type_display,
		deserialized_operation.details,
		deserialized_operation.amount_in_cents,
		deserialized_operation.state,
		deserialized_operation.ignored_from_charts,
		bank_account,
		deserialized_operation.hash,
		tags
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
