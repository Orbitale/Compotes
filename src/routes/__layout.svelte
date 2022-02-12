<script lang="ts">
    import Navigation from "$lib/components/Navigation.svelte";
    import {SvelteToast} from "@zerodevx/svelte-toast";
    import { listen } from '@tauri-apps/api/event';

    /**
     * Post-Startup actions
     */
    import {onMount} from 'svelte';
    import message from "$lib/utils/message";

    onMount(async () => {
        const unlistenMessage = await listen('message', event => {
            message(event.payload.title, event.payload.type.toLowerCase());
        });
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
