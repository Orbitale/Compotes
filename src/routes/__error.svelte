<script context="module">
	/** @type {import('@sveltejs/kit').ErrorLoad} */
	export function load({ error, status }) {
		return {
			props: {
				message: error.message,
				status: status
			}
		};
	}
</script>

<script>
	export let message;
	export let status;
</script>

<h1>An error occurred</h1>

<div class="alert alert-danger">
	<strong>{status}:</strong>
	{message}
</div>
