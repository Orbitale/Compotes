<script lang="ts">
    import ItemLine from "./ItemLine.svelte";
    import {onMount} from "svelte";
    import Field from "$lib/struct/Field.ts";
    import UrlAction from "$lib/struct/UrlAction.ts";
    import type {Writable} from "svelte/store";
    import SpinLoader from "$lib/components/SpinLoader.svelte";

    export let items: object[] = [];
    export let items_store: Writable<any>;
    export let fields: Field[];
    export let actions: UrlAction[] = [];

    let number_per_page = 20;
    let page = 1;
    let number_of_pages = 1;
    let number_of_items;
    let displayed_items = [];
    let store_executed_at_least_once = false;

    onMount(() => {
        if (items_store) {
            if (items.length) {
                throw new Error('Items and item store cannot be defined at the same time, to avoid conflicts when the store updates.');
            }
            items_store.subscribe(store_items => {
                items = store_items;
                displayitems();
                store_executed_at_least_once = true;
            });
        }

        firstPage();
    });

    function firstPage() {
        page = 1;
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
        number_of_items = items ? items.length : 0;
        number_of_pages = items ? Math.ceil(number_of_items / number_per_page) : 1;
        if (number_of_pages < 1) number_of_pages = 1;

        displayed_items = items ? items.slice((page - 1) * number_per_page, (page) * number_per_page) : [];
    }
</script>

<style lang="scss">
    table {
        font-size: 0.8em;
    }
    .actions-header {
        text-align: center;
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

    #no_elements {
      padding: 16px;
    }
</style>

{#if !fields || !fields.length}
    <div class="alert alert-danger">
        No fields were configured. Cannot display table.
    </div>
{:else}
    <table class="table table-bordered table-responsive table-striped table-sm">
        <thead>
            <tr>
                <td colspan="{fields.length + (actions.length ? 1 : 0)}">
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
                {#if actions.length}
                    <th class="actions-header">Actions</th>
                {/if}
            </tr>
        </thead>

        <tbody>
            {#if displayed_items.length}
                {#each displayed_items as item, key (displayed_items[key])}
                    <ItemLine item={displayed_items[key]} {fields} {actions} />
                {/each}
            {:else if items_store && !store_executed_at_least_once}
                <tr>
                    <td colspan={fields.length+(actions.length ? 1 : 0)}>
                        <SpinLoader height={50} as_block={true} />
                    </td>
                </tr>
            {:else}
                <tr>
                    <td colspan={fields.length+(actions.length ? 1 : 0)}>
                        <div id="no_elements">
                            No elements found.
                        </div>
                    </td>
                </tr>
            {/if}
        </tbody>
    </table>
{/if}