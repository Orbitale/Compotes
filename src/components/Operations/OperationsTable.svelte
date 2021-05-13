<script lang="ts">
    import OperationLine from "./OperationLine.svelte";
    import {onMount} from "svelte";
    import {getOperations} from "../../db/operations";

    let number_per_page = 10;
    let page = 1;
    let number_of_pages = 1;
    let all_operations;
    let number_of_operations;
    let displayed_operations = [];

    onMount(async () => {
        all_operations = await getOperations();
        number_of_operations = all_operations.length;
        number_of_pages = Math.ceil(number_of_operations / number_per_page);
        firstPage();
    });

    function firstPage() {
        page = 1;
        displayOperations();
    }

    function nextPage() {
        page++;
        if (page > number_of_pages) {
            page = number_of_pages;
        }
        displayOperations();
    }

    function previousPage() {
        page--;
        if (page < 1) {
            page = 1;
        }
        displayOperations();
    }

    function lastPage() {
        page = number_of_pages;
        displayOperations();
    }

    function displayOperations() {
        displayed_operations = all_operations.slice((page-1)*number_per_page, (page)*number_per_page);

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

<div class="alert alert-info">
    Lines having a <span class="badge bg-warning">colored background</span> correspond to operations that have no tag associated.
    It is highly recommended to use the <i class="fa fa-plus"></i> icon to add new tags to them.
</div>

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
            <th>Date</th>
            <th>Bank account</th>
            <th>Type</th>
            <th>Details</th>
            <th>Amount</th>
            <th>Ignored from charts</th>
            <th>Tags</th>
        </tr>
    </thead>
    <tbody>
        {#each displayed_operations as operation}
            <OperationLine operation={operation} />
        {/each}
    </tbody>
</table>
