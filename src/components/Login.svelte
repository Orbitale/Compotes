<script lang="ts">
    import { users, get_by_id } from '../db/users.ts';
    import message from "../utils/message.ts";
    import {isAuthenticatedStore, userStore} from '../auth/current_user.ts';

    let login_id = '';
    let login_password = '';

    function onFormSubmit (e: Event) {
        e.stopPropagation();
        e.preventDefault();

        if (!login_id) {
            message('Please select a user.');

            return;
        }

        const user = get_by_id(login_id);

        if (!user) {
            message('Invalid user.');

            return;
        }
        if (!user.isValidPassword(login_password)) {
            message('Invalid password.');

            return;
        }

        isAuthenticatedStore.set(true);
        userStore.set(user);
        message('Successfully logged in!');

        return true;
    }
</script>

<form id="login" on:submit={onFormSubmit}>
    <label for="login_username" class="form-label">Username</label>
    <select name="login_username" id="login_username" class="form-select" bind:value={login_id}>
        <option value="">- Select a username -</option>
        {#each users as {id, username}}
            <option value="{id}">{username}</option>
        {/each}
    </select>

    <label for="login_password" class="form-label">Password</label>
    <input type="password" class="form-control" id="login_password" bind:value={login_password}>

    <button type="submit" class="btn btn-primary">
        Login
    </button>
</form>
