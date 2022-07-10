<script lang="ts">
	import Navigation from '$lib/components/Navigation.svelte';
	import { SvelteToast } from '@zerodevx/svelte-toast';
	import { onLoad } from './configure_app.ts';
	import { onMount } from 'svelte';

	onMount(async () => {
		await onLoad();
	});
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
