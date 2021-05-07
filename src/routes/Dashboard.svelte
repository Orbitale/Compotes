<script lang="ts">
    import {getUser} from '../auth/current_user.ts';
    import {replace} from 'svelte-spa-router';
    import {onMount} from "svelte";
    import OperationsTable from "../components/Dashboard/OperationsTable.svelte";

    let user = getUser();

    onMount(async () => {
        user = getUser();

        if (!user || !user.id) {
            return await replace('#/');
        }
    });
</script>

<OperationsTable />
