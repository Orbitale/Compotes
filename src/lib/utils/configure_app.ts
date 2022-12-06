import OperationsSynchronizer from '$lib/struct/OperationsSynchronizer';
import { getOperations, getTriageOperations } from '$lib/db/operations';
import { updateConfig as updateAdminConfig } from '$lib/admin/src/config';
import { getBankAccounts } from '$lib/db/bank_accounts';
import SavedFilter from '$lib/admin/SavedFilter';
import FilterWithValue from '$lib/admin/FilterWithValue';
import FilterType from '$lib/admin/FilterType';
import { DateTime } from 'luxon';
import { getTags } from '$lib/db/tags';
import { getTagRules } from '$lib/db/tag_rules';

const DATE_FORMAT = 'yyyy-MM-dd';
const now = DateTime.now();
const first_day_of_this_year = now.set({ months: 1, days: 1 });
const last_day_of_this_year = now.set({ months: 12, days: 31 });
const first_day_of_last_year = first_day_of_this_year.minus({ years: 1 });
const last_day_of_last_year = last_day_of_this_year.minus({ years: 1 });

let configured = false;

function splash_message(message): HTMLElement {
	console.info(`Configuration log: "${message}".`);
	let container = document.getElementById('splash_messages');
	if (!container) {
		console.warn('Cannot add splash message when container is not present in the DOM.');
		return;
	}
	let textElement = document.createElement('div');
	textElement.style.opacity = '1';
	textElement.style.transition = 'opacity 2s linear 1s';
	textElement.innerText = message;

	let loaderElement = document.createElement('span');
	loaderElement.innerText = ' 🌘';
	loaderElement.style.marginLeft = '3px';
	loaderElement.style.display = 'inline-block';
	loaderElement.style.position = 'relative';
	loaderElement.classList.add('animate-rotation');
	loaderElement.addEventListener('message_end', () => {
		loaderElement.classList.remove('animate-rotation');
		loaderElement.innerText = ' 🌝';
		textElement.style.opacity = '0';
	});
	textElement.appendChild(loaderElement);

	container.appendChild(textElement);

	return loaderElement;
}

function disable_message(element: HTMLElement) {
	if (!element) {
		console.warn('Cannot disable message for inexistent element.');
		return;
	}
	element.dispatchEvent(new Event('message_end'));
}

export default async function configure() {
	let e;

	console.info('Configuring...');

	e = splash_message('Configuring administration panel');
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
	disable_message(e);

	if (configured) {
		console.warn('An attempt to reconfigure the app occurred.');
		return;
	}

	configured = true;

	// Preload everything
	e = splash_message('Configuring synchronization');
	OperationsSynchronizer.addAfterSyncCallback(getOperations);
	OperationsSynchronizer.addAfterSyncCallback(getTriageOperations);
	OperationsSynchronizer.addAfterSyncCallback(getBankAccounts);
	OperationsSynchronizer.addAfterSyncCallback(getTags);
	OperationsSynchronizer.addAfterSyncCallback(getTagRules);
	disable_message(e);

	e = splash_message('Pre-fetching data');
	Promise
		.allSettled([
			fetch('/analytics'),
			fetch('/bank-accounts'),
			fetch('/operations'),
			fetch('/tag-rules'),
			fetch('/tags'),
			fetch('/triage'),
			getOperations(),
			getTags(),
			getTagRules(),
			getTriageOperations(),
		])
		.then(function() { disable_message(e) })
		.then(function () {
			let el = document.getElementById('splash_screen');
			el.style.opacity = '0';
			document.getElementById('app').style.display = '';
			setTimeout(function() {
				document.body.removeChild(el);
			}, 2000);
		})
	;

	console.info('Finished configuring ✔');
}
