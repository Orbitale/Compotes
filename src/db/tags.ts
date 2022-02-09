// @ts-ignore
import Tag from '../entities/Tag';
import api_call from "../utils/api_call";

let tags: Tag[] = [];

export async function getTags(): Promise<Array<Tag>>
{
    if (!tags.length) {
        let res: string = await api_call("get_tags");
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
            return Promise.resolve(tag);
        }
    }

    return Promise.resolve(null);
}

export async function saveTag(tag: Tag): Promise<void>
{
    await api_call("save_tag", {tag: tag.serialize()});

    const tag_entity = await getTagById(tag.id.toString());

    if (!tag_entity) throw new Error('Data corruption detected in tags.');

    tag_entity.mergeWith(tag);
}
