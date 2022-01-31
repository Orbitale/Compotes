<script lang="ts">
    import {getUser} from '../auth/current_user.ts';
    import active from 'svelte-spa-router/active';
    import api_call from "../utils/api_call";
    import message from "../utils/message";
    import {ToastType} from "../struct/Toast";

    let user = getUser();

    let syncing = false;

    let sync = function() {
        if (syncing) {
            return;
        }
        message("Starting sync", ToastType.info);
        syncing = true;
        api_call("sync")
            .then(function (result) {
                if (result === '1') {
                    message('Done!', ToastType.success);
                } else {
                    message('An unknown internal issue has occurred.', ToastType.error);
                }
            })
            .catch(function (error: Error) {
                message(`An error occurred:\n${error.message}`, ToastType.error);
            })
            .finally(function () {
                syncing = false;
            })
        ;
    };
</script>

<style lang="scss">
    .loader {
      display: none;
    }
    .syncing {
      color: darkgrey;
      .loader {
        display: inline;
      }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#/">Home</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav nav-fill">
                {#if user}
                    <li class="nav-item"><a class="nav-link" use:active={"/analytics"} href="#/analytics">
                        Analytics
                    </a></li>
                    <li class="nav-item"><a class="nav-link" use:active={"/operations"} href="#/operations">
                        Operations
                    </a></li>
                    <li class="nav-item"><a class="nav-link" use:active={"/bank-accounts"} href="#/bank-accounts">
                        Bank accounts
                    </a></li>
                    <li class="nav-item"><a class="nav-link" use:active={"/triage"} href="#/triage">
                        Triage
                    </a></li>
                    <li class="nav-item"><a class="nav-link" use:active={"/tag-rules"} href="#/tag-rules">
                        Tag rules
                    </a></li>
                    <li class="nav-item"><a class="nav-link" use:active={"/tags"} href="#/tags">
                        Tags
                    </a></li>
                    <li class="nav-item"><a class="nav-link" use:active={"/import"} href="#/import">
                        Import
                    </a></li>
                    <li class="nav-item" class:syncing><a class="nav-link" href="#" on:click={sync}>
                        Sync
                        <i class="loader">🌙</i>
                    </a></li>
                {/if}
            </ul>
        </div>
    </div>
</nav>
