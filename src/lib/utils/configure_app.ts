import OperationsSynchronizer from '$lib/struct/OperationsSynchronizer';
import { getOperations, getTriageOperations } from '$lib/db/operations';
import { updateConfig as updateAdminConfig } from '$lib/admin/src/config';
import { getBankAccounts } from '$lib/db/bank_accounts';
import SavedFilter from '$lib/admin/src/SavedFilter';
import FilterWithValue from '$lib/admin/src/FilterWithValue';
import FilterType from '$lib/admin/src/FilterType';
import { DateTime } from 'luxon';
import { getTags } from '$lib/db/tags';
import { getTagRules } from '$lib/db/tag_rules';

const DATE_FORMAT = 'yyyy-MM-dd';
const now = DateTime.now();
const first_day_of_this_year = now.set({ months: 1, days: 1 });
const last_day_of_this_year = now.set({ months: 12, days: 31 });
const first_day_of_last_year = first_day_of_this_year.minus({ years: 1 });
const last_day_of_last_year = last_day_of_this_year.minus({ years: 1 });

export default async function configure() {
	console.info('Configuring...');

	console.info('Configuring administration panel');
	updateAdminConfig({
		spinLoaderSrc: '/logo.svg',
		builtinFilters: {
			operations: [
				new SavedFilter('<All operations>', []),
				new SavedFilter('<This year>', [
					new FilterWithValue(
						'operation_date',
						FilterType.date,
						first_day_of_this_year.toFormat(DATE_FORMAT) +
							';' +
							last_day_of_this_year.toFormat(DATE_FORMAT)
					)
				]),
				new SavedFilter('<Last year>', [
					new FilterWithValue(
						'operation_date',
						FilterType.date,
						first_day_of_last_year.toFormat(DATE_FORMAT) +
							';' +
							last_day_of_last_year.toFormat(DATE_FORMAT)
					)
				])
			]
		}
	});

	// Preload everything
	console.info('Configuring synchronization');
	OperationsSynchronizer.clearSyncCallbacks();
	OperationsSynchronizer.addAfterSyncCallback(getOperations);
	OperationsSynchronizer.addAfterSyncCallback(getTriageOperations);
	OperationsSynchronizer.addAfterSyncCallback(getBankAccounts);
	OperationsSynchronizer.addAfterSyncCallback(getTags);
	OperationsSynchronizer.addAfterSyncCallback(getTagRules);

	console.info('Finished configuring ✔');
}
