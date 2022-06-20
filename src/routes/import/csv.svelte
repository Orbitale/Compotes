<script lang="ts">
	import {error, success, warning} from '$lib/utils/message';
	import DragDropList from '$lib/components/DragDrop/DragDropList.svelte';
	import api_call from '$lib/utils/api_call.ts';
	import {bankAccountsStore as bankAccounts} from '$lib/db/bank_accounts';
	import type BankAccount from '$lib/entities/BankAccount';
	import Operation, {OperationState} from '$lib/entities/Operation';
	import {CsvFieldReference, referenceToEntityProperty} from '$lib/utils/csv';
	import {DateFormat} from '$lib/utils/date';

	let file: File|null = null;
	let fileContent: string|null = null;
	let files: FileList|null = null;
	let preview: string|null = null;
	let previewOperations: Array<Array<String>> = [];
	let finalOperations: Array<Operation> = [];

	const numberOfCsvFieldsReferences = 5;

	let csvFields = [
		CsvFieldReference.DATE,
		CsvFieldReference.TYPE,
		CsvFieldReference.TYPE_DISPLAY,
		CsvFieldReference.DETAILS,
		CsvFieldReference.AMOUNT
	];

	let numberOfLinesToRemove: number = 1;
	let separator: string = ';';
	let delimiter: string = '"';
	let escapeCharacter: string = '\\';
	let dateFormat: DateFormat = DateFormat.YMD_SLASH;
	let bankAccount: BankAccount = null;

	let loading = false;

	function resetPreview() {
		preview = null;
		previewOperations = [];
		finalOperations = [];
	}

	function reset() {
		resetPreview();
		file = null;
		fileContent = null;
		files = null;
		loading = false;
	}

	function uploadFile() {
		resetPreview();
		loading = true;

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
		reader.onload = async () => {
			try {
				await readCsvFile(reader);
			} finally {
				loading = false;
			}
		};
		reader.readAsText(file);
	}

	async function readCsvFile(reader: FileReader) {
		fileContent = reader.result.toString().trim();

		const firstLine = fileContent.split('\n')[0] || null;
		if (!firstLine) {
			error('This file seems to be empty, or is not a CSV file.');
			reset();
			return;
		}
		determineCsvParameters(firstLine);

		const previewOperationsFromData = getCsvFromData(fileContent);

		try {
			await denormalizeIntoOperations(previewOperationsFromData);
		} catch (e) {
			error(e.message || e);

			return;
		}

		previewOperations = previewOperationsFromData;
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
			Object.values(CsvFieldReference).forEach((fieldReference: CsvFieldReference) => {
				if (csvFields.indexOf(fieldReference) < 0) {
					csvFields.push(fieldReference);
				}
			});
		}
	}

	async function importFile() {
		if (!bankAccount || !bankAccount.id) {
			warning('Please specify a bank account.');
			return;
		}

		if (!finalOperations || !finalOperations.length) {
			warning('No operation was resolved from this CSV file.');
			return;
		}

		await api_call('import_operations', { operations: finalOperations });

		success(`Successfully imported ${finalOperations.length} operations!`);

		reset();
	}

	async function denormalizeIntoOperations(previewOperations: Array<Array<any>>) {
		let operations = [];

		if (!bankAccount || !bankAccount.id) {
			throw new Error('Bank account is not set.\nPlease set the bank account in the proper field.');
		}

		for (const normalized of previewOperations) {
			const index = previewOperations.indexOf(normalized);
			if (index < numberOfLinesToRemove) {
				continue;
			}
			let normalizedWithKeys = {
				DATE: null,
				AMOUNT: null,
				TYPE: null,
				TYPE_DISPLAY: null,
				DETAILS: null
			};
			normalized.forEach((value, index) => {
				const csvField = csvFields[index];
				if (!csvField) {
					throw `Invalid Csv field index ${index}`;
				}
				const property = referenceToEntityProperty(csvField);
				normalizedWithKeys[property] = value;
			});

			let normalizedDate: string;
			let amount: number;

			try {
				normalizedDate = Operation.normalizeDate(normalizedWithKeys.DATE, dateFormat).toString();
				amount = Operation.normalizeAmount(normalizedWithKeys.AMOUNT);
			} catch (e) {
				throw new Error(
					`CSV line number "${index}" does not contain a valid date.\nError:\n${e.message}`
				);
			}

			let operation = new Operation(
				0, // id
				normalizedDate, // operation_date
				normalizedWithKeys.TYPE, // op_type
				normalizedWithKeys.TYPE_DISPLAY, // type_display
				normalizedWithKeys.DETAILS, // details
				amount, // amount_in_cents
				OperationState.ok, // state
				false, // ignored_from_charts
				bankAccount, // bank_account
				'', // hash
				[] // tags
			);
			await operation.recomputeHash();
			operations.push(operation);
		}

		finalOperations = operations;
	}

	function getCsvFromData(strData: string) {
		const objPattern = new RegExp(
			'(' +
				escapeCharacter +
				separator +
				'|\r?\n|\r|^)' +
				'(?:' +
				delimiter +
				'([^' +
				delimiter +
				']*(?:' +
				delimiter +
				'' +
				delimiter +
				'[^' +
				delimiter +
				']*)*)' +
				delimiter +
				'|' +
				'([^' +
				delimiter +
				escapeCharacter +
				separator +
				'\r\n]*))',
			'gi'
		);

		const arrData = [[]];
		let arrMatches;
		let strMatchedValue: string;

		while ((arrMatches = objPattern.exec(strData))) {
			const strMatchedDelimiter = arrMatches[1];
			if (strMatchedDelimiter.length && strMatchedDelimiter != separator) {
				arrData.push([]);
			}

			if (arrMatches[2]) {
				strMatchedValue = arrMatches[2].replace(/""/g, '"');
			} else {
				strMatchedValue = arrMatches[3];
			}

			arrData[arrData.length - 1].push(strMatchedValue);
		}

		return arrData;
	}
</script>

<a href="/import" class="btn btn-link">&larr; Back to import</a>

<hr />

<div class="row">
	<div class="col">
		<input type="file" id="file_to_import" bind:files on:change={uploadFile} />
	</div>
</div>
<br />
<div class="row">
	<div class="col-lg-4">
		<button
			class="btn btn-outline-info"
			type="button"
			on:click={uploadFile}
			class:disabled={!(!loading && fileContent && fileContent.length)}
		>
			🔄 Refresh
		</button>
		<br />
		<small class="muted">(Remember to refresh on any change)</small>
	</div>

	<div class="col-lg-3">
		<button
			class="btn btn-primary"
			type="button"
			on:click={importFile}
			class:disabled={finalOperations.length === 0}
		>
			Import
		</button>
	</div>
</div>

{#if loading}
	<div class="alert alert-info">Loading…</div>
{/if}

<hr />

<h3>CSV parameters</h3>

<div class="row">
	<div class="col form-group">
		<label class="form-control-label required" for="import_operations_csvSeparator">
			Values separator
		</label>
		<div class="form-widget">
			<select
				bind:value={separator}
				id="import_operations_csvSeparator"
				name="import_operations[csvSeparator]"
				class="form-control"
			>
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
			<select
				bind:value={delimiter}
				id="import_operations_csvDelimiter"
				name="import_operations[csvDelimiter]"
				class="form-control"
			>
				<option value="&quot;" selected="selected">"</option>
				<option value="'">'</option>
				<option value="" />
			</select>
		</div>
	</div>

	<div class="col form-group">
		<label class="form-control-label required" for="import_operations_csvEscapeCharacter">
			Escape character
		</label>
		<div class="form-widget">
			<select
				bind:value={escapeCharacter}
				id="import_operations_csvEscapeCharacter"
				name="import_operations[csvEscapeCharacter]"
				class="form-control"
			>
				<option value="\" selected="selected">\</option>
				<option value="" />
			</select>
		</div>
	</div>
</div>
<div class="row rowWithTopPadding">
	<div class="col form-group">
		<label class="form-control-label required" for="import_operations_dateFormat">
			Date format
		</label>
		<div class="form-widget">
			<select
				bind:value={dateFormat}
				id="import_operations_dateFormat"
				name="import_operations[dateFormat]"
				class="form-control"
			>
				{#each Object.values(DateFormat) as format}
					<option value={format}>{format}</option>
				{/each}
			</select>
		</div>
	</div>

	<div class="col form-group">
		<label class="form-control-label required" for="import_operations_linesToRemove">
			Number of first lines to remove
		</label>
		<input
			bind:value={numberOfLinesToRemove}
			id="import_operations_linesToRemove"
			type="number"
			class="form-control"
			min="0"
		/>
	</div>

	<div class="col form-group">
		<label class="form-control-label required" for="import_operations_bankAccount">
			Bank Account
		</label>
		<div class="form-widget">
			<select
				bind:value={bankAccount}
				id="import_operations_bankAccount"
				name="import_operations[bankAccount]"
				class="form-control"
			>
				{#if $bankAccounts}
					{#each $bankAccounts as account}
						<option value={account}>{account.name}</option>
					{/each}
				{/if}
			</select>
		</div>
	</div>
</div>
<div class="row rowWithTopPadding">
	<div class="col form-group">
		<label for="csv_columns">Csv columns</label>
		<div class="form-widget" id="csv_columns">
			<DragDropList bind:data={csvFields} />
			<p>
				<span class="badge rounded-pill bg-info">ℹ</span> Remember to sort these fields
				<strong>manually</strong> in order to make sure CSV fields are parsed properly by the application.
			</p>
		</div>
	</div>
</div>

<hr />

{#if previewOperations.length}
	<h3>Preview:</h3>
	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr class="table-info">
				<th>#</th>
				{#each csvFields as field}
					<th>{field}</th>
				{/each}
			</tr>
		</thead>
		<tbody>
			{#each previewOperations as line, key}
				<tr class={key < numberOfLinesToRemove ? 'line-to-remove' : ''}>
					<td>{key}</td>
					{#each line as value}
						<td>{value}</td>
					{/each}
				</tr>
			{/each}
		</tbody>
	</table>
{/if}

<style lang="scss">
	.rowWithTopPadding {
		margin-top: 15px;
	}

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
		content: '🗑';
		display: block;
		position: absolute;
		left: -25px;
	}

	.table-info {
		margin-bottom: 5px;
	}
</style>
