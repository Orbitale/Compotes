<script lang="ts">
    import ConfigFilter from "$lib/admin/ConfigFilter";
    import FilterType from "$lib/admin/FilterType";

    export let filter: ConfigFilter;
    export let change_callback: Function;

    let value;

    function onChange() {
        if (change_callback) {
            change_callback(filter, value);
        }
    }

    if (!filter) {
        throw new Error('Must specify a filter object to create a Filter component.');
    }
</script>

<div class="row">
    <label for="input_filter" class=" col-form-label col-sm-2">
        {filter.title || filter.name}
    </label>
    <div class="col-sm-10">
        {#if filter.type === FilterType.text}
            <input id="input_filter" class="form-control" type="text" on:change={onChange} bind:value>
        {/if}
    </div>
</div>
