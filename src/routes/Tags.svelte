<script lang="ts">
    import {needsUser} from '../auth/current_user.ts';
    import {getTags} from "../db/tags.ts";
    import PaginatedTable from "../components/PaginatedTable/PaginatedTable.svelte";
    import Tag from "../entities/Tag.ts";
    import FieldToDisplay from "../struct/FieldToDisplay.ts";
    import {onMount} from "svelte";

    needsUser();

    let tags: Tag[];

    let fields = [
        new FieldToDisplay('id', 'ID'),
        new FieldToDisplay('name', 'Date'),
    ];

    onMount(async () => tags = await getTags())
</script>

<PaginatedTable items={tags} fields={fields} />
