import type Entity from '$lib/struct/Entity';

export default function merge_lists<T extends Entity>(
	current_items: Array<T>,
	new_items: Array<T>
): Array<T> {
	return new_items.reduce(
		function (final: Array<T>, new_object: T): Array<T> {
			const found =
				-1 !== current_items.findIndex((current_object: T) => current_object.id === new_object.id);

			if (!found) {
				final.push(new_object);
			}

			return final;
		},
		[...current_items]
	);
}
