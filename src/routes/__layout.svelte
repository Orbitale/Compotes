<script lang="ts">
    import Navigation from "$lib/components/Navigation.svelte";
    import {SvelteToast} from "@zerodevx/svelte-toast";
    import OperationsSynchronizer from "$lib/struct/OperationsSynchronizer";

    /**
     * Post-Startup actions
     */
    import {onMount} from 'svelte';
    import {refreshAllOperations} from "$lib/db/operations";
    import {updateConfig as updateAdminConfig} from "$lib/admin/src/config";

    updateAdminConfig({
        spinLoaderSrc: '/logo.svg',
    });

    OperationsSynchronizer.addAfterSyncCallback(refreshAllOperations);

    onMount(async () => {
        await refreshAllOperations();
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
