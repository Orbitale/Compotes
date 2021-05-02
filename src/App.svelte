<script lang="ts">
    import Navigation from "./components/Navigation.svelte";
    import Router from 'svelte-spa-router';
    import Home from './routes/Home.svelte';
    import Error from './routes/Error.svelte';
    import Dashboard from './routes/Dashboard.svelte';

    /**
     * Routing
     */
    let routes = {
        '/': Home,
        '/dashboard': Dashboard,
        '*': Error,

    };

    /**
     * Post-Startup actions
     */
    import {onMount} from 'svelte';
    import removeSplashScreen from "./utils/remove_splashscreen.ts";
    onMount(() => {
        removeSplashScreen(document);
    })
</script>

<style global lang="scss">
    @import "./style/_app.scss";
</style>

<Navigation />

<main class="container">
    <Router {routes} />
</main>