<script lang="ts">
    import { users } from '../db/users.ts';
    import message from "../utils/Toast";

    function onFormSubmit (e: Event) {
        e.stopPropagation();
        e.preventDefault();

        message('TODO: Attempt login');

        return false;
    }
</script>

<form id="login" on:submit={onFormSubmit}>
    <label for="login_username" class="form-label">Username</label>
    <select name="login_username" id="login_username" class="form-select">
        <option value="">- Select a username -</option>
        {#each users as {id, username}}
            <option value="{id}">{username}</option>
        {/each}
    </select>

    <label for="login_password" class="form-label">Password</label>
    <input type="password" class="form-control" id="login_password">

    <button type="submit" class="btn btn-primary">
        Login
    </button>
</form>
