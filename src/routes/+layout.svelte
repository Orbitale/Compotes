<script lang="ts">
	import '../style/_app.scss';
	import Navigation from '$lib/components/Navigation.svelte';
	import { SvelteToast } from '@zerodevx/svelte-toast';
	import {onMount} from "svelte";
	import {
		afterNavigate,
		beforeNavigate,
	} from '$app/navigation';
	import configure from "$lib/utils/configure_app";

	let loadingOverlay: HTMLElement;
	let loadingTimeout: number = 0;

	onMount(() => {
		configure();
	});

	beforeNavigate(() => {
		if (loadingTimeout) {
			clearTimeout(loadingTimeout);
			loadingTimeout = 0;
		}
		loadingTimeout = setTimeout(() => {
			loadingOverlay.style.display = "block";
		}, 100);
	});

	afterNavigate(() => {
		clearTimeout(loadingTimeout);
		loadingTimeout = 0;
		loadingOverlay.style.display = "none";
	});
</script>

<div id="loading-overlay" bind:this={loadingOverlay}>Loading...</div>

<div id="toast_container">
	<SvelteToast />
</div>

<Navigation />

<main class="container">
	<slot />
</main>

<style lang="scss">
	#loading-overlay {
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		-webkit-backdrop-filter: blur(5px);
		backdrop-filter: blur(5px);
		z-index: 10;
		text-align: center;
		line-height: 100vh;
	}
	#toast_container {
		--toastContainerTop: auto;
		--toastContainerRight: 1rem;
		--toastContainerBottom: 0.5rem;
		--toastContainerLeft: auto;
	}
</style>
