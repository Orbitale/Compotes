<script lang="ts">
	import { dashboard } from '$lib/crud/Dashboard';
</script>

<svelte:component this={dashboard.adminConfig.theme.dashboard} {dashboard}>
	<h1>{dashboard.adminConfig.head.brandName}</h1>

	<ul>
		{#each dashboard.cruds as crud}
			<li>
				<a href="/crud/{crud.name}/{crud.options.defaultOperationName}">
					{crud.options.label.plural}
				</a>
			</li>
		{/each}
	</ul>
</svelte:component>
