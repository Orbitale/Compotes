<script lang="ts">
	import Button from 'carbon-components-svelte/src/Button/Button.svelte';
	import Grid from 'carbon-components-svelte/src/Grid/Grid.svelte';
	import Row from 'carbon-components-svelte/src/Grid/Row.svelte';
	import Column from 'carbon-components-svelte/src/Grid/Column.svelte';
	import InlineNotification from 'carbon-components-svelte/src/Notification/InlineNotification.svelte';
	import Select from 'carbon-components-svelte/src/Select/Select.svelte';
	import SelectItem from 'carbon-components-svelte/src/Select/SelectItem.svelte';
	import NumberInput from 'carbon-components-svelte/src/NumberInput/NumberInput.svelte';
	import Table from 'carbon-components-svelte/src/DataTable/Table.svelte';
	import TableBody from 'carbon-components-svelte/src/DataTable/TableBody.svelte';
	import TableCell from 'carbon-components-svelte/src/DataTable/TableCell.svelte';
	import TableContainer from 'carbon-components-svelte/src/DataTable/TableContainer.svelte';
	import TableHead from 'carbon-components-svelte/src/DataTable/TableHead.svelte';
	import TableHeader from 'carbon-components-svelte/src/DataTable/TableHeader.svelte';
	import TableRow from 'carbon-components-svelte/src/DataTable/TableRow.svelte';

	import TrashCan from 'carbon-icons-svelte/lib/TrashCan.svelte';

	import { error, success, warning } from '$lib/utils/message';
	import DragDropList from '$lib/components/DragDrop/DragDropList.svelte';
	import api_call from '$lib/utils/api_call';
	import Operation, { OperationState } from '$lib/entities/Operation';
	import { CsvFieldReference, referenceToEntityProperty } from '$lib/utils/csv';
	import { DateFormat } from '$lib/utils/date';
	import type BankAccount from '$lib/entities/BankAccount';
	import { onMount } from 'svelte';
	import { getBankAccounts } from '$lib/db/bank_accounts';

	let file: File | null = null;
	let fileContent: string | null = null;
	let files: FileList | null = null;
	let previewOperations: Array<Array<string>> = [];
	let finalOperations: Array<Operation> = [];
	let bankAccounts: Array<BankAccount> = [];

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
	let dateFormat: DateFormat = DateFormat.DMY_SLASH;
	let bankAccountId: string | number | undefined;

	let loading = false;

	function resetPreview() {
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
		fileContent = (reader.result || '').toString().trim();

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
		if (!bankAccountId) {
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

	async function denormalizeIntoOperations(previewOperations: Array<Array<unknown>>) {
		let operations = [];

		if (!bankAccountId) {
			throw new Error('Bank account is not set.\nPlease set the bank account in the proper field.');
		}

		const bankAccount = bankAccounts.filter(
			(acc: BankAccount) => acc.id.toString() === bankAccountId?.toString()
		)[0];
		if (!bankAccount) {
			throw new Error(`Invalid bank account id "${bankAccountId}".`);
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
				normalizedWithKeys.TYPE || '', // op_type
				normalizedWithKeys.TYPE_DISPLAY || '', // type_display
				normalizedWithKeys.DETAILS || '', // details
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

	onMount(async () => {
		bankAccounts = await getBankAccounts();
	});
</script>

<Button kind="ghost" href="/import" class="btn btn-link">&larr; Back to import</Button>

<hr />

<Grid padding>
	<Row>
		<Column>
			<input type="file" id="file_to_import" bind:files on:change={uploadFile} />
		</Column>
	</Row>
	<Row>
		<Column>
			<Button
				kind="secondary"
				type="button"
				on:click={uploadFile}
				disabled={!(!loading && fileContent && fileContent.length)}
			>
				🔄 Refresh
			</Button>
			<br />
			<small class="muted">(Remember to refresh on any change)</small>
		</Column>
		<Column>
			<Button type="button" on:click={importFile} disabled={finalOperations.length === 0}>
				Import
			</Button>
		</Column>
	</Row>
	{#if loading}
		<Row>
			<Column>
				<InlineNotification>Loading…</InlineNotification>
			</Column>
		</Row>
	{/if}
	<h3>CSV parameters</h3>
	<Row>
		<Column>
			<label for="import_operations_csvSeparator"> Values separator </label>
			<Select
				bind:selected={separator}
				id="import_operations_csvSeparator"
				name="import_operations[csvSeparator]"
			>
				<SelectItem value=";" />
				<SelectItem value="," />
			</Select>
		</Column>
		<Column>
			<label for="import_operations_csvDelimiter"> Text delimiter </label>
			<Select
				bind:selected={delimiter}
				id="import_operations_csvDelimiter"
				name="import_operations[csvDelimiter]"
			>
				<SelectItem value="&quot;" />
				<SelectItem value="'" />
				<SelectItem value="" />
			</Select>
		</Column>
		<Column>
			<label for="import_operations_csvEscapeCharacter"> Escape character </label>
			<Select
				bind:selected={escapeCharacter}
				id="import_operations_csvEscapeCharacter"
				name="import_operations[csvEscapeCharacter]"
			>
				<SelectItem value={String.fromCharCode(92)} />
				<SelectItem value="" />
			</Select>
		</Column>
	</Row>
	<Row>
		<Column>
			<label for="import_operations_dateFormat"> Date format </label>
			<div class="form-widget">
				<Select
					bind:selected={dateFormat}
					id="import_operations_dateFormat"
					name="import_operations[dateFormat]"
				>
					{#each Object.values(DateFormat) as format}
						<SelectItem value={format} />
					{/each}
				</Select>
			</div>
		</Column>
		<Column>
			<label for="import_operations_linesToRemove"> Number of first lines to remove </label>
			<NumberInput
				bind:value={numberOfLinesToRemove}
				id="import_operations_linesToRemove"
				min={0}
			/>
		</Column>
		<Column>
			<label for="import_operations_bankAccount"> Bank Account </label>
			<Select
				bind:selected={bankAccountId}
				id="import_operations_bankAccount"
				name="import_operations[bankAccount]"
			>
				{#each bankAccounts as account}
					<SelectItem value={account.id} text={account.name} />
				{/each}
			</Select>
		</Column>
	</Row>
</Grid>

<hr />
<br />

<label for="csv_columns">Csv columns</label>
<DragDropList bind:data={csvFields} />
<p>
	<span class="badge rounded-pill bg-info">ℹ</span> Remember to sort these fields
	<strong>manually</strong> in order to make sure CSV fields are parsed properly by the application.
</p>

<br />
<hr />

{#if previewOperations.length}
	<h3>Preview:</h3>
	<TableContainer>
		<Table zebra size="short">
			<TableHead>
				<TableRow>
					<TableHeader>#</TableHeader>
					{#each csvFields as field}
						<TableHeader>{field}</TableHeader>
					{/each}
				</TableRow>
			</TableHead>
			<TableBody>
				{#each previewOperations as line, key}
					<TableRow class={key < numberOfLinesToRemove ? 'line-to-remove' : ''}>
						<TableCell>
							{key}
							{#if key < numberOfLinesToRemove}
								(<TrashCan />)
							{/if}
						</TableCell>
						{#each line as value}
							<TableCell>
								{value}
								{#if key < numberOfLinesToRemove}
									(<TrashCan />)
								{/if}
							</TableCell>
						{/each}
					</TableRow>
				{/each}
			</TableBody>
		</Table>
	</TableContainer>
{/if}

<style>
	:global(main table tbody tr.line-to-remove td) {
		opacity: 0.3;
		color: black !important;
		background: #fff !important;
	}
</style>
