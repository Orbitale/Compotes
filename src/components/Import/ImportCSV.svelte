<script lang="ts">
    import {error, warning} from "../../utils/message";
    import DragDropList from "../DragDrop/DragDropList.svelte";
    import api_fetch from "../../utils/api_fetch.ts";
    import {getBankAccounts} from '../../db/bank_accounts';
    import type BankAccount from "../../entities/BankAccount";
    import Operation, {OperationState} from "../../entities/Operation";
    import {onMount} from "svelte";
    import {CsvFieldReference, DateFormat} from "../../utils/import";

    let bankAccounts: Array<BankAccount> = [];
    let file: File = null;
    let fileContent: string = null;
    let files: FileList = null;
    let preview: string = '';
    let previewOperations: Array<Array<String>> = [];
    let finalOperations: Array<Operation> = [];

    const numberOfCsvFieldsReferences = 5;
    const csvFieldsReferences: Array<CsvFieldReference> = Object.values(CsvFieldReference);
    const referenceToEntityProperty = (ref) => {
        for (let key in Object.keys(CsvFieldReference)) {
            if (!csvFieldsReferences.hasOwnProperty(key)) continue;
            if (csvFieldsReferences[key] === ref) {
                return key;
            }
        }
        throw `Invalid csv field ${ref}.`;
    };
    const csvFieldsReferencesList = [
        CsvFieldReference.DATE,
        CsvFieldReference.TYPE,
        CsvFieldReference.TYPE_DISPLAY,
        CsvFieldReference.DETAILS,
        CsvFieldReference.AMOUNT,
    ];

    let csvFields = [
        CsvFieldReference.DATE,
        CsvFieldReference.TYPE,
        CsvFieldReference.TYPE_DISPLAY,
        CsvFieldReference.DETAILS,
        CsvFieldReference.AMOUNT,
    ];

    let numberOfLinesToRemove: number = 1;
    let separator: string = ';';
    let delimiter: string = '"';
    let escapeCharacter: string = '\\';
    let dateFormat: DateFormat = DateFormat.YMD_SLASH;
    let bankAccount: BankAccount = null;

    function reset() {
        file = null;
        fileContent = null;
        files = null;
        preview = null;
        previewOperations = [];
        finalOperations = [];
    }

    function uploadFile() {
        if (!files || !files.length) {
            warning('Please upload a CSV file.');
            reset();
            return;
        }

        if (files.length > 1) {
            warning('Only one CSV file per import is supported (for now).');
            reset();
            return;
        }

        file = files[0];
        let reader = new FileReader();
        reader.onload = () => readCsvFile(reader);
        reader.readAsText(file);
    }

    function readCsvFile(reader: FileReader) {
        fileContent = reader.result.toString();

        const firstLine = fileContent.split("\n")[0] || null;
        if (!firstLine) {
            error("This file seems to be empty, or is not a CSV file.");
            reset();
            return;
        }
        determineCsvParameters(firstLine);

        previewOperations = getCsvFromData(fileContent);
        denormalizeIntoOperations();

        const firstOperation: Array<string> = previewOperations[0] ?? null;
        if (!firstOperation) { return; }
    }

    function determineCsvParameters(firstLine: string) {
        if (firstLine.match(/^[^;]+;/)) {
            separator = ';';
        } else if (firstLine.match(/^[^,]+,/)) {
            separator = ',';
        }

        if (firstLine.match(/^[^']+'/)) {
            delimiter = "'";
        } else if (firstLine.match(/^[^"]+"/)) {
            delimiter = '"';
        }

        let values = getCsvFromData(firstLine)[0] || [];

        values.forEach((value: string, index) => {
            if (!csvFields[index]) return;

            if (value.match(/date/gi)) {
                csvFields[index] = CsvFieldReference.DATE;
            }

            if (value.match(/display/gi)) {
                csvFields[index] = CsvFieldReference.TYPE_DISPLAY;
            } else if (value.match(/type/gi)) {
                csvFields[index] = CsvFieldReference.TYPE;
            }

            if (value.match(/amount|montant/gi)) {
                csvFields[index] = CsvFieldReference.AMOUNT;
            }

            if (value.match(/details|label/gi)) {
                csvFields[index] = CsvFieldReference.DETAILS;
            }
        });

        // Remove duplicates
        csvFields = csvFields.filter((value, index, self) => self.indexOf(value) === index);

        if (csvFields.length !== numberOfCsvFieldsReferences) {
            csvFieldsReferencesList.forEach((fieldReference) => {
                if (csvFields.indexOf(fieldReference) < 0) {
                    csvFields.push(fieldReference);
                }
            });
        }
    }

    async function importFile() {
        await api_fetch("import_csv", {
            finalOperations,
            bankAccountId: bankAccount.id,
        });
    }

    function denormalizeIntoOperations() {
        let operations = [];

        previewOperations.forEach((normalized, index) => {
            if (index < numberOfLinesToRemove) {
                return;
            }
            let normalizedWithKeys = {};
            normalized.forEach((value, index) => {
                const csvField = csvFields[index];
                if (!csvField) {
                    throw `Invalid Csv field index ${index}`
                }
                const property = referenceToEntityProperty(csvField);
                normalizedWithKeys[property] = value;
            });
            let operation = new Operation(
                0, //id
                Operation.normalizeDate(normalizedWithKeys.DATE, dateFormat).toString(), //operation_date
                normalizedWithKeys.TYPE, //op_type
                normalizedWithKeys.TYPE_DISPLAY, //type_display
                normalizedWithKeys.DETAILS, //details
                Operation.normalizeAmount(normalizedWithKeys.AMOUNT), //amount_in_cents
                '', //hash
                OperationState.pending_triage, //state
                false, //ignored_from_charts
                bankAccount.id, //bank_account_id
            );
            operation.sync();
            operations.push(operation);
        });

        finalOperations = operations;
        debugger;
    }

    function getCsvFromData(strData: string) {
        const objPattern = new RegExp(
            '(' + escapeCharacter + separator + '|\r?\n|\r|^)' +
            '(?:'+delimiter+'([^'+delimiter+']*(?:'+delimiter+''+delimiter+'[^'+delimiter+']*)*)'+delimiter+'|' +
            '([^'+delimiter+escapeCharacter+separator+'\r\n]*))'
        , 'gi');

        const arrData = [[]];
        let arrMatches;
        let strMatchedValue: string;

        while (arrMatches = objPattern.exec(strData)) {
            const strMatchedDelimiter = arrMatches[1];
            if (strMatchedDelimiter.length && (strMatchedDelimiter != separator)) {
                arrData.push([]);
            }

            if (arrMatches[2]) {
                strMatchedValue = arrMatches[2].replace(/""/g, '"');
            } else {
                strMatchedValue = arrMatches[3];
            }

            arrData[arrData.length-1].push(strMatchedValue);
        }

        return arrData;
    }

    onMount(async () => {
        bankAccounts = await getBankAccounts();
    });
</script>

<div>
    <input type="file" id="file_to_import" bind:files on:change={uploadFile} />
</div>

<div>
    {#if fileContent && fileContent.length}
        <button class="btn btn-primary" type="button" on:click={importFile}>Import</button>
    {/if}
</div>

<hr>

<h3>CSV parameters</h3>

<div class="row">

    <div class="col form-group">
        <label class="form-control-label required" for="import_operations_csvSeparator">
            Values separator
        </label>
        <div class="form-widget">
            <select bind:value={separator} on:change={uploadFile} id="import_operations_csvSeparator" name="import_operations[csvSeparator]" class="form-control">
                <option value=";" selected="selected">;</option>
                <option value=",">,</option>
            </select>
        </div>
    </div>

    <div class="col form-group">
        <label class="form-control-label required" for="import_operations_csvDelimiter">
            Text delimiter
        </label>
        <div class="form-widget">
            <select bind:value={delimiter} on:change={uploadFile} id="import_operations_csvDelimiter" name="import_operations[csvDelimiter]" class="form-control">
                <option value="&quot;" selected="selected">"</option>
                <option value="'">'</option>
                <option value=""></option>
            </select>
        </div>
    </div>

    <div class="col form-group">
        <label class="form-control-label required" for="import_operations_csvEscapeCharacter">
            Escape character
        </label>
        <div class="form-widget">
            <select bind:value={escapeCharacter} on:change={uploadFile} id="import_operations_csvEscapeCharacter" name="import_operations[csvEscapeCharacter]"
                    class="form-control">
                <option value="\" selected="selected">\</option>
                <option value=""></option>
            </select>
        </div>
    </div>

    <div class="col form-group">
        <label class="form-control-label required" for="import_operations_dateFormat">
            Date format
        </label>
        <div class="form-widget">
            <select bind:value={dateFormat} on:change={uploadFile} id="import_operations_dateFormat" name="import_operations[dateFormat]" class="form-control">
                {#each Object.values(DateFormat) as format}
                    <option value="{format}">{format}</option>
                {/each}
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col form-group">
        <label for="csv_columns">Csv columns</label>
        <div class="form-widget" id="csv_columns">
            <DragDropList bind:data={csvFields}/>
            <p>
                <span class="badge rounded-pill bg-info">ℹ</span> Remember to sort these fields <strong>manually</strong> in order to
                make sure CSV fields are parsed properly by the application.
            </p>
        </div>
    </div>
    <div class="col form-group">
        <label class="form-control-label required" for="import_operations_linesToRemove">
            Number of first lines to remove
        </label>
        <input bind:value={numberOfLinesToRemove} id="import_operations_linesToRemove" type="number" class="form-control" min="0">
    </div>
    <div class="col form-group">
        <label class="form-control-label required" for="import_operations_bankAccount">
            Bank Account
        </label>
        <div class="form-widget">
            <select bind:value={bankAccount} id="import_operations_bankAccount" name="import_operations[bankAccount]" class="form-control">
                {#each bankAccounts as account}
                    <option value={account}>{account.name}</option>
                {/each}
            </select>
        </div>
    </div>
</div>

<hr>

<h3>Preview:</h3>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr class="table-info">
            {#each csvFields as field}
                <th>{field}</th>
            {/each}
        </tr>
    </thead>
    <tbody>
        {#each previewOperations as line, key}
            <tr class="{key < numberOfLinesToRemove ? 'line-to-remove' : ''}">
                {#each line as value}
                    <td>{value}</td>
                {/each}
            </tr>
        {/each}
    </tbody>
</table>

<style lang="scss">
  th {
    font-weight: normal;
  }

  .line-to-remove {
    opacity: 0.5;
  }

  .line-to-remove,
  .line-to-remove td {
    position: relative;
  }

  .line-to-remove td:first-child::before {
    content: "🗑";
    display: block;
    position: absolute;
    left: -25px;
  }

  .table-info {
    margin-bottom: 5px;
  }
</style>