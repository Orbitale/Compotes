<script lang="ts">
    import {getUser} from '../auth/current_user.ts';
    import {invoke} from "@tauri-apps/api/tauri";
    import {replace} from 'svelte-spa-router';
    import {onMount} from "svelte";

    let user = getUser();

    onMount(async () => {
        user = getUser();

        if (!user || !user.id) {
            return await replace('#/');
        }

        console.info('Mounted!');

        console.info('Resetted user!');

        let res = await invoke("get_operations");

        console.info(res);
    });
</script>

Hey {user?.username}!
