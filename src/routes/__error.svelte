<script context="module">
	/** @type {import('@sveltejs/kit').ErrorLoad} */
	export function load({ error, status }) {
		return {
			props: {
				error_message: `${status}: ${error.message}`
			}
		};
	}
</script>

<script>
	export let error_message;
</script>

<h1>An error occurred</h1>

<div class="alert-danger">
    {error_message}
</div>