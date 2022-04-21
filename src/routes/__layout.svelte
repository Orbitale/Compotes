<script lang="ts">
	import Navigation from '$lib/components/Navigation.svelte';
	import {SvelteToast} from '@zerodevx/svelte-toast';
	import './configure_app.ts';
</script>

<div id="toast_container">
	<SvelteToast />
</div>

<Navigation />

<main class="container">
	<slot />
</main>

<style global lang="scss">
	@import '../style/_app.scss';
	#toast_container {
		--toastContainerTop: auto;
		--toastContainerRight: 1rem;
		--toastContainerBottom: 0.5rem;
		--toastContainerLeft: auto;
	}
</style>
