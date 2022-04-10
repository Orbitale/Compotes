// @ts-ignore
import Tag from '$lib/entities/Tag';
import api_call from '$lib/utils/api_call';
import { Writable, writable } from 'svelte/store';

export const tagsStore: Writable<Tag[]> = writable();

let tags: Tag[] = [];

export async function getTags(): Promise<Array<Tag>> {
	if (!tags.length) {
		let res: string = await api_call('tags_get');
		tags = JSON.parse(res).map((data: object) => {
			// @ts-ignore
			return new Tag(data.id, data.name);
		});

		tagsStore.set(tags);
	}

	return tags;
}

export async function getTagById(id: number): Promise<Tag | null> {
	if (!tags.length) {
		await getTags();
	}

	for (const tag of tags) {
		if (tag.id === id) {
			return Promise.resolve(tag);
		}
	}

	return Promise.resolve(null);
}

export async function getTagsByIds(ids: Array<number>): Promise<Array<Tag>> {
	if (!tags.length) {
		await getTags();
	}

	let tags_found: Array<Tag> = [];

	for (const id of ids) {
		const tag = await getTagById(id);
		if (!tag) {
			throw new Error(`Could not find tag with id ${id}`);
		}
		tags_found.push(tag);
	}

	return Promise.resolve(tags_found);
}

export async function updateTag(tag: Tag): Promise<void> {
	await api_call('tag_update', { tag: tag.serialize() });

	const tag_entity = await getTagById(tag.id);

	if (!tag_entity) throw new Error('Data corruption detected in tags.');

	tag_entity.mergeWith(tag);
}

export async function createTag(tag: Tag): Promise<void> {
	let id = await api_call('tag_create', { tag: tag.serialize() });

	if (isNaN(+id)) {
		throw new Error('Internal error: API returned a non-number ID.');
	}

	tag.setId(+id);
}
