<script lang="ts">
    import {getSavedFilters} from "$lib/admin/src/filters.ts";
    import {onMount} from "svelte";
    import SavedFilter from "$lib/admin/SavedFilter.ts";

    let filters: Array<SavedFilter> = [];

    onMount(() => {
        filters = getSavedFilters();
    });
</script>

<h1>Analytics (Work in progress)</h1>

<hr>

<h2>Available filters:</h2>
<ul>
    {#each filters as filter}
        <li>
            {filter.name}
        </li>
    {/each}
</ul>