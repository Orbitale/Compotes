<script lang="ts">
    import {needsUser} from '../auth/current_user.ts';
    import TagsTable from "../components/Tags/TagsTable.svelte";

    needsUser();
</script>

<TagsTable />
