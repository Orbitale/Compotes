<script lang="ts">
	import { getUsers, getUserById } from '$lib/db/users.ts';
	import { error, success } from '$lib/utils/message.ts';
	import { setUser } from '$lib/auth/current_user.ts';

	let login_id = '';
	let login_password = '';

	async function onFormSubmit(e: Event) {
		e.stopPropagation();
		e.preventDefault();

		if (!login_id) {
			error('Please select a user.');

			return;
		}

		const user = getUserById(login_id);

		if (!user) {
			error('Invalid user.');

			return;
		}

		if (!user.isValidPassword(login_password)) {
			error('Invalid password.');

			return;
		}

		setUser(user);

		success('Successfully logged in!');

		location.href = '/operations';

		return false;
	}
</script>

<form id="login" on:submit={onFormSubmit}>
	<label for="login_username" class="form-label">Username</label>
	<select name="login_username" id="login_username" class="form-select" bind:value={login_id}>
		<option value="">- Select a username -</option>
		{#each getUsers() as { id, username }}
			<option value={id}>{username}</option>
		{/each}
	</select>

	<label for="login_password" class="form-label">Password</label>
	<input type="password" class="form-control" id="login_password" bind:value={login_password} />

	<button type="submit" class="btn btn-primary"> Login </button>
</form>
