// @ts-ignore
import Tag from '../entities/Tag.ts';
import {invoke} from "@tauri-apps/api/tauri";

let tags: Tag[] = [];

export async function getTags(): Promise<Array<Tag>>
{
    if (!tags.length) {
        let res: string = await invoke("get_tags");
        tags = JSON.parse(res);
    }

    return tags;
}

export async function getTagById(id: string): Promise<Tag | null>
{
    if (!tags.length) {
        await getTags();
    }

    for (const tag of tags) {
        if (tag.id.toString() === id) {
            return tag;
        }
    }

    return null;
}
