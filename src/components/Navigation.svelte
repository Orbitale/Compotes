<script lang="ts">
    import {getUser} from '../auth/current_user.ts';

    let user = getUser();
</script>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#/">Home</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                {#if user}
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#/dashboard">Dashboard</a>
                    </li>
                {/if}
            </ul>
        </div>
    </div>
</nav>
