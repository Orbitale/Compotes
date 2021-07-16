<script lang="ts">
    import {warning} from "../../utils/message";
    import DragDropList from "../DragDrop/DragDropList.svelte";
    import * as Papaparse from "papaparse";

    let file: File = null;
    let fileContent: string = null;
    let filePreview: string = '';
    let files: FileList = null;
    let numberOfLinesToRemove: number = 1;

    let csvContent = [];

    let csvFields = [
        'Date',
        'Type',
        'Type (display)',
        'Details',
        'Amount',
    ];

    function handleDndConsider(e) {
        csvFields = e.detail.items;
    }
    function handleDndFinalize(e) {
        csvFields = e.detail.items;
    }

    function reset() {
        file = null;
        fileContent = null;
        filePreview = '';
        files = null;
    }

    function uploadFile() {

        if (!files || !files.length) {
            warning('Please upload a CSV file.');
            reset();
            return;
        }

        if (files.length > 1) {
            warning('Only one OFX file per import is supported (for now).');
            reset();
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

            csvContent = Papaparse.parse(fileContent);

            console.info({csvContent});

            // TODO
        };

        reader.readAsText(file);
    }

</script>

<div>
    <input type="file" id="file_to_import" bind:files/>
</div>

<div>
    <button class="btn btn-primary" type="button" on:click={uploadFile}>Import</button>
</div>

<h3>Preview:</h3>
<pre id="preview">
    {filePreview}
</pre>

{#if filePreview.length || true}
    <h3>CSV parameters</h3>

    <div class="row">

        <div class="col form-group">
            <label class="form-control-label required" for="import_operations_csvSeparator">
                Csv separator
            </label>
            <div class="form-widget">
                <select id="import_operations_csvSeparator" name="import_operations[csvSeparator]" class="form-control">
                    <option value=";" selected="selected">;</option>
                    <option value=",">,</option>
                </select>
            </div>
        </div>

        <div class="col form-group">
            <label class="form-control-label required" for="import_operations_csvDelimiter">
                Csv delimiter
            </label>
            <div class="form-widget">
                <select id="import_operations_csvDelimiter" name="import_operations[csvDelimiter]" class="form-control">
                    <option value="&quot;" selected="selected">"</option>
                    <option value="'">'</option>
                    <option value=""></option>
                </select>
            </div>
        </div>

        <div class="col form-group">
            <label class="form-control-label required" for="import_operations_csvEscapeCharacter">
                Csv escape character
            </label>
            <div class="form-widget">
                <select id="import_operations_csvEscapeCharacter" name="import_operations[csvEscapeCharacter]" class="form-control">
                    <option value="\" selected="selected">\</option>
                    <option value=""></option>
                </select>
            </div>
        </div>

        <div class="col form-group">
            <label class="form-control-label required" for="import_operations_csvEscapeCharacter">
                Number of first lines to remove
            </label>
            <div class="form-widget">
                <input type="number" min="0" bind:value={numberOfLinesToRemove}>
            </div>
        </div>
    </div>

    <div class="col form-group">
        <label for="csv_columns">Csv columns</label>
        <div class="form-widget">

            <DragDropList bind:data={csvFields} />

            <ol>
                {#each csvFields as csvField, i}
                    <li>
                        <input class="form-control" type="hidden" id="csv_columns_{i}" name="csv_columns[{i}]" value="{csvField}">
                        {csvField}
                    </li>
                {/each}
            </ol>

        </div>
    </div>
{/if}
