<script lang="ts">
    import ImportCSV from "../components/Import/ImportCSV.svelte";
</script>

<style lang="scss">
    .tab-pane {
        padding-top: 15px;
    }
</style>

<h1>Import (TODO)</h1>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#csv" type="button" role="tab" aria-selected="true">CSV</button>
    </li>
</ul>

<div class="tab-content" id="tabs">
    <div class="tab-pane fade show active" id="choose" role="tabpanel" aria-labelledby="choose">
        Choose an import format.
    </div>

    <div class="tab-pane fade" id="csv" role="tabpanel" aria-labelledby="csv">
        <ImportCSV />
    </div>
</div>

