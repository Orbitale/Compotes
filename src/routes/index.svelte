<script lang="ts">
    // import Login from "$lib/components/Login.svelte";
    // import Register from "$lib/components/Register.svelte";

    import {getUser} from '$lib/auth/current_user.ts';
    import {onMount} from "svelte";
    import type User from "$lib/struct/User";

    let user: User;

    onMount(async () => {
        user = await getUser();
    });
</script>

<div class="container">
    <h1 class="container">Compotes app, new version!</h1>

    <!-- TODO: check if  this is relevant
    {#if user === true}
        Hello {user.username}!
    {:else}
        <div class="accordion" id="homeaccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#loginCollapse" aria-expanded="true" aria-controls="loginCollapse">
                        Login
                    </button>
                </h2>
                <div id="loginCollapse" class="accordion-collapse collapse show" data-bs-parent="#homeaccordion">
                    <div class="accordion-body">
                        <Login/>
                    </div>
                </div>
            </div>
            <div id="login" class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#registerCollapse" aria-expanded="true" aria-controls="registerCollapse">
                        Create a new account
                    </button>
                </h2>
                <div id="registerCollapse" class="accordion-collapse collapse" data-bs-parent="#homeaccordion">
                    <div class="accordion-body">
                        <Register/>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    -->
</div>

<style lang="scss">
    h1 {
        text-align: center;
        padding: 1em;
        margin: 0 auto;
    }
</style>
