<script lang="ts">
    import {getUser} from '../auth/current_user.ts';

    let user = getUser();
</script>

Hey {user.username}!
