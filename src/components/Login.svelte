<script>
    import {Stronghold, Location} from 'tauri-plugin-stronghold-api'

    import {user_id} from '../utils/stores.ts';
</script>

<h2>Login</h2>

<form id="login">
    <label for="login_username" class="col-sm-2 col-form-label">Username</label>
    <select name="login_username" id="login_username" class="form-select">
        <option value="">- Select a username -</option>
    </select>
    <label for="login_password" class="col-sm-2 col-form-label">Password</label>
    <div class="col-sm-10">
        <input type="password" class="form-control" id="login_password">
    </div>
</form>
