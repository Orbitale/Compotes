<script lang="ts">
    import message from "../utils/message.ts";

    function onFormSubmit(e: Event) {
        e.stopPropagation();
        e.preventDefault();

        message('TODO: Attempt register');

        return false;
    }
</script>

<form id="register" on:submit={onFormSubmit} method="post" action="#">
    <label for="register_username" class="form-label">Username</label>
    <input type="text" class="form-control" id="register_username">

    <label for="register_password" class="form-label">Password</label>
    <input type="password" class="form-control" id="register_password">

    <label for="register_password_repeat" class="form-label">Repeat password</label>
    <input type="password" class="form-control" id="register_password_repeat">

    <button type="submit" class="btn btn-primary">
        Register
    </button>
</form>
