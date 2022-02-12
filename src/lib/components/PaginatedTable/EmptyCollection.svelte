<script lang="ts">
    import EmptyCollectionMessageLine from './EmptyCollectionMessageLine.svelte';
    import Field from "$lib/struct/Field";

    export let fields: Field[];
</script>

<table class="table table-bordered table-responsive table-striped table-sm">
    <thead>
        <tr>
            {#each fields as field}
                <th>{field.text}</th>
            {/each}
        </tr>
    </thead>
    <tbody>
        <EmptyCollectionMessageLine number_of_fields={fields.length} />
    </tbody>
</table>
