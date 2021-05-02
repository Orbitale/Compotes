<script lang="ts">
    import {getUsers, getUserById} from '../db/users.ts';
    import message from "../utils/message.ts";
    import {setUser, getUser} from '../auth/current_user.ts';
    import {replace} from 'svelte-spa-router'
    import {onMount} from "svelte";

    let login_id = '';
    let login_password = '';

    onMount(() => {
        let user = getUser();
        console.info('debug user', {user});
        if (user) {
            replace('#/dashboard');
        }
    });

    function onFormSubmit (e: Event) {
        e.stopPropagation();
        e.preventDefault();

        if (!login_id) {
            message('Please select a user.');

            return;
        }

        const user = getUserById(login_id);

        if (!user) {
            message('Invalid user.');

            return;
        }

        if (!user.isValidPassword(login_password)) {
            message('Invalid password.');

            return;
        }

        setUser(user);

        message('Successfully logged in!');

        replace('#/dashboard');

        return true;
    }
</script>

<form id="login" on:submit={onFormSubmit}>
    <label for="login_username" class="form-label">Username</label>
    <select name="login_username" id="login_username" class="form-select" bind:value={login_id}>
        <option value="">- Select a username -</option>
        {#each getUsers() as {id, username}}
            <option value="{id}">{username}</option>
        {/each}
    </select>

    <label for="login_password" class="form-label">Password</label>
    <input type="password" class="form-control" id="login_password" bind:value={login_password}>

    <button type="submit" class="btn btn-primary">
        Login
    </button>
</form>
