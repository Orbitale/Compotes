<script lang="ts">
    import {onMount} from "svelte";
    import {dashboard} from "$lib/crud/Dashboard";

    onMount(() => {
        const app = document.getElementById('app');

        if (!app) throw new Error('No app element displayed: did you forget to check the main layout and "app.html" files?');

        app.style.display = '';

        const splash_screen = document.getElementById('splash_screen');
        if (splash_screen) {

            setTimeout(function () {
                splash_screen.style.opacity = '0';
                splash_screen.style.display = 'none';
                splash_screen.style.visibility = 'hidden';
            }, 400);
        }
    });
</script>

<svelte:component this="{dashboard.adminConfig.theme.dashboard}" {dashboard}>
    <slot />
</svelte:component>
