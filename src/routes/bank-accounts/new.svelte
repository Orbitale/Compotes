<script lang="ts">
    import {goto} from '$app/navigation';
    import {createBankAccount} from "$lib/db/bank_accounts.ts";
    import type bank_account from "$lib/entities/BankAccount.ts";
    import {error, success} from "$lib/utils/message.ts";
    import BankAccount from "$lib/entities/BankAccount.ts";

    let bank_account: BankAccount = BankAccount.empty();
    let submit_button_disabled: boolean = false;

    function onNameChange() {
        submit_button_disabled = !bank_account.name;
    }

    async function submitForm(e: Event) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        try {
            await createBankAccount(bank_account);
        } catch (e) {
            error(e.message);
            return;
        }

        success('Bank account saved!');
        await goto('/bank-accounts');

        return false;
    }

</script>

<form action="#" on:submit={submitForm} >

    <h2>Create bank account</h2>

    <div class="row">
        <label for="name" class="col-form-label col-sm-2">
            Name
        </label>
        <div class="col-sm-10">
            <input autocomplete="" type="text" id="name" bind:value={bank_account.name} on:keyup={onNameChange} class="form-control">
        </div>
    </div>

    <div class="row">
        <label for="currency" class="col-form-label col-sm-2">
            Currency
        </label>
        <div class="col-sm-10">
            <input autocomplete="" type="text" id="currency" bind:value={bank_account.currency} on:keyup={onNameChange} class="form-control">
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-sm-2">&nbsp;</div>
        <div class="col-sm-10">
            <button type="submit" class="btn btn-primary {submit_button_disabled ? 'disabled' : ''}" disabled={submit_button_disabled}>
                Save
            </button>
        </div>
    </div>
</form>
