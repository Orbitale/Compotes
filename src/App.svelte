<script global lang="ts">
	import {onMount} from 'svelte';
    import router from 'page';
    import Home from './routes/Home.svelte';
    import removeSplashScreen from "./utils/remove_splashscreen.ts";

    onMount(() => removeSplashScreen())
</script>

<Home />

<style global lang="scss">
	@import "./style/_app.scss";
</style>
