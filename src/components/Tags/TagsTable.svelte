<script lang="ts">
    import TagLine from "./TagLine.svelte";
    import {onMount} from "svelte";
    import {getTags} from "../../db/tags";

    let number_per_page = 10;
    let page = 1;
    let number_of_pages = 1;
    let all_tags;
    let number_of_tags;
    let displayed_tags = [];

    onMount(async () => {
        all_tags = await getTags();
        number_of_tags = all_tags.length;
        number_of_pages = Math.ceil(number_of_tags / number_per_page);
        firstPage();
    });

    function firstPage() {
        page = 1;
        displayTags();
    }

    function nextPage() {
        page++;
        if (page > number_of_pages) {
            page = number_of_pages;
        }
        displayTags();
    }

    function previousPage() {
        page--;
        if (page < 1) {
            page = 1;
        }
        displayTags();
    }

    function lastPage() {
        page = number_of_pages;
        displayTags();
    }

    function displayTags() {
        displayed_tags = all_tags.slice((page-1)*number_per_page, (page)*number_per_page);

    }
</script>

<style lang="scss">
    table {
        font-size: 0.8em;
    }
    #previous-page, #next-page {
        font-size: 2.5em;
        width: 2.5em;
        height: 2.5em;
    }
    #pages-text {
        text-align: center;
        line-height: 4em;
        font-size: 1.5em;
    }
    #previous-page { float: left; }
    #next-page { float: right; }
</style>

<table class="table table-bordered table-responsive table-striped table-sm">
    <thead>
    <tr>
        <td colspan="8">
                <button type="button" class="btn btn-outline-primary" disabled="{page === 1}" on:click={previousPage} id="previous-page">&lt;</button>
                <button type="button" class="btn btn-outline-primary" disabled="{page === number_of_pages}" on:click={nextPage} id="next-page">&gt;</button>
            <div id="pages-text">
                Page: {page} / {number_of_pages}
            </div>
        </td>
    </tr>
    <tr>
        <th>ID</th>
        <th>Name</th>
    </tr>
    </thead>
    <tbody>
    {#each displayed_tags as tag}
        <TagLine tag={tag} />
    {/each}
    </tbody>
</table>
