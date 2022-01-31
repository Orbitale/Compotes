<script lang="ts">
    import Navigation from "./components/Navigation.svelte";
    import Router from 'svelte-spa-router';
    import routes from './routes.ts';

    /**
     * Post-Startup actions
     */
    import {onMount} from 'svelte';
    import removeSplashScreen from "./utils/remove_splashscreen.ts";
    import message from "./utils/message";

    onMount(async () => {
        removeSplashScreen(document);
        window['message'] = message;
    })
</script>

<style global lang="scss">
    @import "./style/_app.scss";
</style>

<Navigation/>

<main class="container">
    <Router {routes}/>
</main>