<script lang="ts">
	import { getBankAccountById, updateBankAccount } from '$lib/db/bank_accounts.ts';
	import type bank_account from '$lib/entities/BankAccount.ts';
	import { error, success } from '$lib/utils/message.ts';
	import BankAccount from '$lib/entities/BankAccount.ts';
	import { page } from '$app/stores';
	import { onMount } from 'svelte';

	export let id: string = $page.params.id;

	let bank_account: BankAccount = BankAccount.empty();
	let submit_button_disabled: boolean = false;

	onMount(async () => {
		bank_account = await getBankAccountById(parseInt(id, 10));
		if (!bank_account || !bank_account.id) {
			throw new Error(`Invalid bank account id ${id}`);
		}
	});

	function onNameChange() {
		submit_button_disabled = !(bank_account.name && bank_account.currency);
	}

	function validateCurrencyCode() {
		bank_account.currency = bank_account.currency.toUpperCase();
		if (bank_account.currency) {
			try {
				new Intl.NumberFormat(navigator.language, {
					style: 'currency',
					currency: bank_account.currency
				}).format(1);
			} catch (e) {
				if (e.message.match('Invalid currency code')) {
					error(e.message);
					bank_account.currency = '';
				}
			}
		}
	}

	async function submitForm(e: Event) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();

		try {
			await updateBankAccount(bank_account);
		} catch (e) {
			error(e.message || e);
			return;
		}

		success('Bank account updated!');

		return false;
	}
</script>

<form action="#" on:submit={submitForm}>
	<h2>Update bank account</h2>

	<div class="row">
		<label for="name" class="col-form-label col-sm-2"> Name </label>
		<div class="col-sm-10">
			<input
				autocomplete=""
				type="text"
				id="name"
				bind:value={bank_account.name}
				on:keyup={onNameChange}
				class="form-control"
			/>
		</div>
	</div>

	<div class="row">
		<label for="currency" class="col-form-label col-sm-2"> Currency </label>
		<div class="col-sm-10">
			<input
				autocomplete=""
				type="text"
				id="currency"
				bind:value={bank_account.currency}
				on:keyup={onNameChange}
				on:change={validateCurrencyCode}
				class="form-control"
			/>
		</div>
	</div>

	<br />

	<div class="row">
		<div class="col-sm-2">&nbsp;</div>
		<div class="col-sm-10">
			<button
				type="submit"
				class="btn btn-primary {submit_button_disabled ? 'disabled' : ''}"
				disabled={submit_button_disabled}
			>
				Save
			</button>
		</div>
	</div>
</form>
