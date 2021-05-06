<script lang="ts">
    import {getUser} from '../auth/current_user.ts';
    import {invoke} from "@tauri-apps/api/tauri";
    import {replace} from 'svelte-spa-router';
    import {onMount} from "svelte";
    import OperationLine from "../components/Dashboard/OperationLine.svelte";

    let user = getUser();

    let page = 1;
    let total = 0;
    let all_operations = [];
    let operations = [];

    onMount(async () => {
        user = getUser();

        if (!user || !user.id) {
            return await replace('#/');
        }

        let res: String = await invoke("get_operations");
        all_operations = JSON.parse(res);

        total = all_operations.length;
        operations = all_operations.slice(0, 10);
    });
</script>

{#each operations as operation}
    <OperationLine operation={operation} />
{/each}
