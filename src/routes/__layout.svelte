<script lang="ts">
    import Navigation from "$lib/components/Navigation.svelte";
    import {SvelteToast} from "@zerodevx/svelte-toast";
    import { listen } from '@tauri-apps/api/event';
    import OperationsSynchronizer from "$lib/struct/OperationsSynchronizer";

    /**
     * Post-Startup actions
     */
    import {onMount} from 'svelte';
    import message from "$lib/utils/message";
    import {getOperations, getTriageOperations} from "$lib/db/operations";

    onMount(async () => {
        const _unlistenMessage = await listen('message', event => {
            message(event.payload.title, event.payload.type.toLowerCase());
        });

        OperationsSynchronizer.addAfterSyncCallback(async () => await getOperations());
        OperationsSynchronizer.addAfterSyncCallback(async () => await getTriageOperations());
    })
</script>

<style global lang="scss">
  @import "../style/_app.scss";
</style>

<SvelteToast />

<Navigation/>

<main class="container">

    <slot></slot>

</main>
