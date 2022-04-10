import api_call from '$lib/utils/api_call';
import type { Result } from 'ts-results-es';
import { Err, Ok } from 'ts-results-es';

export default class OperationsSynchronizer {
	private static _afterSyncCallbacks: Array<Function> = [];

	static addAfterSyncCallback(callback: Function) {
		OperationsSynchronizer._afterSyncCallbacks.push(callback);
	}

	public static async sync(): Promise<Result<string, string>> {
		try {
			const result = await api_call('sync');

			const parsedResult = JSON.parse(result);
			const { rules_applied, affected_operations, duplicates_refreshed } = parsedResult;
			if (typeof rules_applied !== 'undefined' && typeof duplicates_refreshed !== 'undefined') {
				let msg =
					'Synced!\n' +
					(rules_applied > 0
						? `Applied ${rules_applied} tag rules to ${affected_operations} operations.\n`
						: 'No tag rules to apply.\n') +
					(duplicates_refreshed > 0
						? `Detected ${duplicates_refreshed} new duplicates for triage.\n`
						: 'No new duplicate operations.\n');
				OperationsSynchronizer._afterSyncCallbacks.forEach((callback: Function) => callback());
				return Ok(msg);
			} else {
				return Err('An unknown internal issue has occurred.');
			}
		} catch (error) {
			return Err(`An error occurred:\n${error.message}`);
		}
	}
}
