<script lang="ts">
	import Field from '../../src/Field';

	export let field: Field;
	export let item: object;

	let field_value = field.displayFromItem(item);
</script>

{#if field_value === null}
	<span class="badge bg-dark">Empty</span>
{:else if field_value === ''}
	<span class="badge bg-dark">Empty string</span>
{:else if field_value === undefined}
	<span class="badge bg-dark">Undefined</span>
{:else if field_value === true || field_value === false}
	{#if field_value === true}
		<span class="badge rounded-pill bg-success">&check;</span>
	{:else}
		<span class="badge rounded-pill bg-danger">&times;</span>
	{/if}
{:else if field_value instanceof Array}
	{#each field_value as array_value}
		<span class="badge bg-secondary">{array_value}</span>
	{/each}
{:else if typeof field_value === 'object'}
	<pre>{JSON.stringify(field_value)}</pre>
{:else}
	{field_value}
{/if}
