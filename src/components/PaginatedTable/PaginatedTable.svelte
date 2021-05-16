<script lang="ts">
    import ItemLine from "./ItemLine.svelte";
    import {onMount} from "svelte";
    import FieldToDisplay from "../../struct/FieldToDisplay.ts";

    export let items: object[];
    export let fields: FieldToDisplay[];

    let number_per_page = 10;
    let page = 1;
    let number_of_pages = 1;
    let number_of_items;
    let displayed_items = [];

    function firstPage() {
        page = 1;
        displayitems();
    }

    function nextPage() {
        page++;
        if (page > number_of_pages) {
            page = number_of_pages;
        }
        displayitems();
    }

    function previousPage() {
        page--;
        if (page < 1) {
            page = 1;
        }
        displayitems();
    }

    function lastPage() {
        page = number_of_pages;
        displayitems();
    }

    function displayitems() {
        displayed_items = items.slice((page - 1) * number_per_page, (page) * number_per_page);
    }

    onMount(() => {

        number_of_items = items.length;
        number_of_pages = Math.ceil(number_of_items / number_per_page);

        firstPage();
    });
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
        {#each fields as field}
            <th>{field.text}</th>
        {/each}
    </tr>
    </thead>
    <tbody>
    {#if displayed_items.length}
        {#each displayed_items as item}
            <ItemLine item={item} fields={fields} />
        {/each}
    {/if}
    </tbody>
</table>
