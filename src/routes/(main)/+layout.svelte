<script lang="ts">
	import { dashboard } from '$lib/crud/Dashboard';
</script>

<svelte:component this={dashboard.adminConfig.theme.dashboard} {dashboard}>
	<slot />
</svelte:component>
