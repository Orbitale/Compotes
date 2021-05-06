<script lang="ts">
    import Operation from "../../entities/Operation";
    import OperationLine from "./OperationLine.svelte";

    export let operations: Array<Operation>;
</script>

{#each operations as operation}
    <OperationLine operation={operation} />
{/each}
