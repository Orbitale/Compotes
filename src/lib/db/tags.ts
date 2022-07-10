// @ts-ignore
import Tag from '$lib/entities/Tag';
import api_call from '$lib/utils/api_call';
import { writable } from 'svelte/store';
import type { Writable } from 'svelte/store';

export const tagsStore: Writable<Tag[]> = writable();

let tags_promise: Promise<Tag[]> | null = null;

async function getTagsPromise(): Promise<Array<Tag>> {
	if (!tags_promise) {
		tags_promise = getTags();
	}

	return tags_promise;
}

export async function getTags(): Promise<Array<Tag>> {
	if (tags_promise) {
		return await tags_promise;
	}

	let res: string = await api_call('tags_get');

	const tags = JSON.parse(res).map((data: object) => {
		// @ts-ignore
		return new Tag(data.id, data.name);
	});

	tagsStore.set(tags);

	return tags;
}

export async function getTagById(id: number): Promise<Tag | null> {
	const tags = await getTagsPromise();

	for (const tag of tags) {
		if (tag.id === id) {
			return tag;
		}
	}

	return null;
}

export async function getTagsByIds(ids: Array<number>): Promise<Array<Tag>> {
	const tags = await getTagsPromise();

	let tags_found: Array<Tag> = [];

	for (const id of ids) {
		let found = false;
		for (const tag of tags) {
			if (tag.id === id) {
				tags_found.push(tag);
				found = true;
				break;
			}
		}

		if (!found) {
			throw new Error(`Could not find tag with id ${id}`);
		}
	}

	return tags_found;
}

export async function updateTag(tag: Tag): Promise<void> {
	await api_call('tag_update', { tag: tag.serialize() });

	const tag_entity = await getTagById(tag.id);

	if (!tag_entity) throw new Error('Data corruption detected in tags.');

	tag_entity.mergeWith(tag);

	tags_promise = null;
}

export async function createTag(tag: Tag): Promise<void> {
	let id = await api_call('tag_create', { tag: tag.serialize() });

	if (isNaN(+id)) {
		throw new Error('Internal error: API returned a non-number ID.');
	}

	tag.setId(+id);

	tags_promise = null;
}
