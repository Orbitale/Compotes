<script lang="ts">
	import { page } from '$app/stores';
</script>

<h1>An error occurred</h1>

<div class="alert alert-danger">
	<strong>{$page.status}:</strong>
	{$page.error.message}
</div>
