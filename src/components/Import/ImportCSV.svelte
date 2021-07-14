<script lang="ts">
    import {warning} from "../../utils/message";

    let file: File = null;
    let fileContent: string = null;
    let filePreview: string = '';
    let files: FileList = null;

    function uploadFile(e) {
        if (!files || !files.length) {
            warning('Please upload a CSV file.');
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

            let contentAsArray = fileContent.split("\n");

            filePreview = contentAsArray.splice(0, 20).join("\n");
            if (contentAsArray.length > 20) {
                filePreview += "\n(…)";
            }

        };

        reader.readAsText(file);

        console.info({file});
    }
</script>

<div>
    <input type="file" id="file_to_import" bind:files />
</div>

<div>
    <button class="btn btn-primary" type="button" on:click={uploadFile}>Import</button>
</div>

Preview:
<pre id="preview">
    {filePreview}
</pre>