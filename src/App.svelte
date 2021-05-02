<script lang="ts">
    import {getUser} from './auth/current_user.ts';
    import Navigation from "./components/Navigation.svelte";
    import router from 'page';
    import Home from './routes/Home.svelte';
    import Error from './routes/Error.svelte';
    import Dashboard from './routes/Dashboard.svelte';

    /**
     * Routing
     */
    let page;
    let params;
    let routes = {

        '/': () => page = Home,

        '/dashboard': () => {
            const user = getUser();
            if (!user) {
                router.redirect('/');
                return;
            }
            page = Dashboard;
        },

        '/*': () => page = Error,

    };
    for (let path in routes) router(path, routes[path]);
    router.start();

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
    <svelte:component this="{page}" params="{params}" />
</main>