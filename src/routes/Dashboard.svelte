<script lang="ts">
    import {getUser} from '../auth/current_user.ts';
    import {replace} from 'svelte-spa-router';

    let user = getUser();

    if (!user) {
        replace('#/');
    }
</script>

Hey {user.username}!
