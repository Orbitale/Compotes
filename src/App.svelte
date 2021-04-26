<script lang="ts">
	import {onMount} from 'svelte';
	import Home from "./pages/Home.svelte";

	onMount(() => {
		setTimeout(() => {
			document.getElementById('splash_screen').classList.add('remove');
		}, 1000);
		setTimeout(() => {
			let splash_screen = document.getElementById('splash_screen');
			let splash_screen_style = document.getElementById('splash_screen_style');
			splash_screen.parentElement.removeChild(splash_screen);
			splash_screen_style.parentElement.removeChild(splash_screen_style);
		}, 4000);
	})
</script>

<Home />

<style global lang="scss">
	@import "./style/_app.scss";

	main {
		text-align: center;
		padding: 1em;
		max-width: 240px;
		margin: 0 auto;
	}

	h1 {
		color: #ff3e00;
		text-transform: uppercase;
		font-size: 4em;
		font-weight: 100;
	}

	@media (min-width: 640px) {
		main {
			max-width: none;
		}
	}
</style>
