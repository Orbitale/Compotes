<script lang="ts">
    import EmptyCollectionMessageLine from './EmptyCollectionMessageLine.svelte';

    export let number_of_fields = 1;
</script>

<table class="table table-bordered table-responsive table-striped table-sm">
    <tbody>
        <EmptyCollectionMessageLine {number_of_fields} />
    </tbody>
</table>
