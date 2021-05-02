<script lang="ts">
    import {userStore, isAuthenticatedStore} from '../auth/current_user.ts';

    let isAuthenticated = false;
    let user = null;

    userStore.subscribe(userInStore => user = userInStore);
    isAuthenticatedStore.subscribe(isAuthenticatedInStore => isAuthenticated = isAuthenticatedInStore);
</script>

<a href="/">Home</a>

{#if isAuthenticated}
    <a href="/dashboard">Dashboard</a>
{/if}
