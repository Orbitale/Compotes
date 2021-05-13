<script lang="ts">
    import Tag from "../../entities/Tag";

    export let tag: Tag;
</script>

<style lang="scss">
    .tag-name
    {
        white-space: nowrap;
    }
</style>

<tr>
    <td>{tag.id}</td>
    <td class="tag-name">{tag.name}</td>
</tr>
