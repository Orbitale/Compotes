// @ts-ignore
import Tag, {serializeTag} from '../entities/Tag.ts';
import api_fetch from "../utils/api_fetch.ts";

let tags: Tag[] = [];

export async function getTags(): Promise<Array<Tag>>
{
    if (!tags.length) {
        let res: string = await api_fetch("get_tags");
        tags = JSON.parse(res).map((data: object) => {
            // @ts-ignore
            return new Tag(data.id, data.name);
        });
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

export async function saveTag(tag: Tag): Promise<void>
{
    await api_fetch("save_tag", {tag: tag.serialize()});

    const tag_entity = await getTagById(tag.id.toString());

    if (!tag_entity) throw new Error('Data corruption detected in tags.');

    tag_entity.mergeWith(tag);
}
