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
                        <a class="nav-link active" aria-current="page" href="#/analytics">Analytics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#/operations">Operations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#/bank-accounts">Bank accounts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#/triage">Triage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#/tag-rules">Tag rules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#/tags">Tags</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#/import">Import</a>
                    </li>
                {/if}
            </ul>
        </div>
    </div>
</nav>
