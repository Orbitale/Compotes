<script lang="ts">
	import { configStore } from '$lib/admin/src/config';
	import { onDestroy } from 'svelte';

	export let display: boolean = true;
	export let height: number = 0;
	export let width: number = 0;
	export let as_block: boolean = false;

	let src: string = '';

	switch (true) {
		case height === 0 && width === 0:
			height = width = 15;
			break;
		case height === 0 && width !== 0:
			height = width;
			break;
		case height !== 0 && width === 0:
			width = height;
			break;
	}

	let display_style = display ? (as_block ? 'block' : 'inline-block') : 'none';

	let configStoreUnsubscribe = configStore.subscribe((config) => (src = config.spinLoaderSrc));

	onDestroy(() => {
		configStoreUnsubscribe();
	});
</script>

<span style="display: {display_style}">
	<img {src} {width} {height} alt="" />
</span>

<style lang="scss">
	@-webkit-keyframes rotating /* Safari and Chrome */ {
		from {
			-ms-transform: rotate(0deg);
			-webkit-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		to {
			-ms-transform: rotate(360deg);
			-webkit-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}

	@keyframes rotating {
		from {
			-ms-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-webkit-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		to {
			-ms-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-webkit-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}

	span {
		text-align: center;
		img {
			animation: rotating 2s linear infinite;
		}
	}
</style>
