<script lang="ts">
	import Navigation from '$lib/components/Navigation.svelte';
	import { SvelteToast } from '@zerodevx/svelte-toast';
	import OperationsSynchronizer from '$lib/struct/OperationsSynchronizer';
	import { updateConfig as updateAdminConfig } from '$lib/admin/src/config';
	import { getOperations, getTriageOperations } from '$lib/db/operations';

	updateAdminConfig({
		spinLoaderSrc: '/logo.svg'
	});

	OperationsSynchronizer.addAfterSyncCallback(getOperations);
	OperationsSynchronizer.addAfterSyncCallback(getTriageOperations);
</script>

<SvelteToast />

<Navigation />

<main class="container">
	<slot />
</main>

<style global lang="scss">
	@import '../style/_app.scss';
</style>
