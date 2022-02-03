<script lang="ts">
    import Navigation from "./components/Navigation.svelte";
    import Router from 'svelte-spa-router';
    import routes from './routes.ts';
    import { listen } from '@tauri-apps/api/event';

    /**
     * Post-Startup actions
     */
    import {onMount} from 'svelte';
    import removeSplashScreen from "./utils/remove_splashscreen.ts";
    import message from "./utils/message";

    onMount(async () => {
        removeSplashScreen(document);

        const unlistenMessage = await listen('message', event => {
            message(event.payload.title, event.payload.type.toLowerCase());
        });
    })
</script>

<style global lang="scss">
    @import "./style/_app.scss";
</style>

<Navigation/>

<main class="container">
    <Router {routes}/>
</main>
