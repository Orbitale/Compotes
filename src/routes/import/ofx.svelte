<script lang="ts">
	import { warning } from '$lib/utils/message';
	import api_call from '$lib/utils/api_call.ts';

	let file: File = null;
	let fileContent: string = null;
	let filePreview: string = '';
	let files: FileList = null;

	function uploadFile() {
		if (!files || !files.length) {
			warning('Please upload a OFX file.');
			return;
		}

		if (files.length > 1) {
			warning('Only one OFX file per import is supported (for now).');
			return;
		}

		file = files[0];
		let reader = new FileReader();

		reader.onload = () => {
			fileContent = reader.result.toString();

			let contentAsArray = fileContent.split('\n');

			filePreview = contentAsArray.splice(0, 20).join('\n');
			if (contentAsArray.length > 20) {
				filePreview += '\n(…)';
			}
		};

		reader.readAsText(file);
	}

	async function importFile() {
		warning('Not implemented yet.');
		await api_call('import_ofx', { fileContent: filePreview });
	}
</script>

<p class="alert alert-warning">Not implemented yet.</p>

<div>
	<input type="file" id="file_to_import" bind:files />
</div>

<div>
	{#if files && files.length > 0}
		<button class="btn btn-primary" type="button" on:click={uploadFile}>Preview</button>
	{/if}
	{#if filePreview && filePreview.length}
		<button class="btn btn-primary" type="button" on:click={importFile}>Import</button>
	{/if}
</div>

{#if filePreview}
	Preview:
	<pre id="preview">
    {filePreview}
</pre>
{/if}
