<script lang="ts">
    import {saveBankAccount} from "../../db/bank_accounts.ts";
    import type bank_account from "../../entities/BankAccount.ts";
    import random_bytes from "../../utils/random.ts";
    import {error, success} from "../../utils/message.ts";
    import {pop} from "svelte-spa-router";
    import BankAccount from "../../entities/BankAccount.ts";

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
            await saveBankAccount(bank_account);
        } catch (e) {
            error(e);
            return;
        }

        success('Bank account saved!');
        await pop();

        return false;
    }

    const rand = '_'+random_bytes(20);

</script>

<form action="#" on:submit={submitForm} >

    <h2>Create bank account</h2>

    <div class="row">
        <label for="name{rand}" class="col-form-label col-sm-2">
            Name
        </label>
        <div class="col-sm-10">
            <input autocomplete="{rand}" type="text" id="name{rand}" bind:value={bank_account.name} on:keyup={onNameChange} class="form-control">
        </div>
    </div>

    <div class="row">
        <label for="currency{rand}" class="col-form-label col-sm-2">
            Currency
        </label>
        <div class="col-sm-10">
            <input autocomplete="{rand}" type="text" id="currency{rand}" bind:value={bank_account.currency} on:keyup={onNameChange} class="form-control">
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
