<script lang="ts">
    import Operation from "../../entities/Operation";

    export let operation: Operation;
</script>

<div class="card-body">
    {operation.id}
    / {operation.op_type}
    / {operation.details}
</div>