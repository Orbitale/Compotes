import { dashboard } from '$lib/crud/Dashboard';

export const prerender = true;

/** @type {import('../../../../../../.svelte-kit/types/src/routes').EntryGenerator} */
export function entries() {
	const routes = [];

	for (const crud of dashboard.cruds) {
		for (const operation of crud.options.operations) {
			routes.push({
				crud: crud.name,
				operation: operation.name
			});
		}
	}

	return routes;
}
