<script lang="ts">
    import {getUser} from '../auth/current_user.ts';
    import active from 'svelte-spa-router/active';
    import OperationsSynchronizer from "../struct/OperationsSynchronizer";

    let syncing: boolean;
    let user = getUser();

    const synchronizer = new OperationsSynchronizer();

    $: syncing = synchronizer.syncing;
</script>

<style lang="scss">
  @-webkit-keyframes rotating /* Safari and Chrome */ {
    from {
      -webkit-transform: rotate(0deg);
      -o-transform: rotate(0deg);
      transform: rotate(0deg);
    }
    to {
      -webkit-transform: rotate(360deg);
      -o-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }
  @keyframes rotating {
    from {
      -ms-transform: rotate(0deg);
      -moz-transform: rotate(0deg);
      -webkit-transform: rotate(0deg);
      -o-transform: rotate(0deg);
      transform: rotate(0deg);
    }
    to {
      -ms-transform: rotate(360deg);
      -moz-transform: rotate(360deg);
      -webkit-transform: rotate(360deg);
      -o-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }
    .loader {
      display: none;
      animation: rotating 2s linear infinite;
    }

    .syncing.nav-item {
      &, * {
        cursor: not-allowed;
      }
      .nav-link {
        pointer-events: none;
        color: #ddd;

        .loader {
          display: inline-block;
        }
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
                    <li class="nav-item" class:syncing><button class="nav-link" on:click={synchronizer.sync}>
                        Sync
                        <img src="logo.svg" width="15px" height="15px" alt="" class="loader">
                    </button></li>
                {/if}
            </ul>
        </div>
    </div>
</nav>
