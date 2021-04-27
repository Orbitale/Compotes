<script lang="ts">
    import Login from "../components/Login.svelte";
    import Register from "../components/Register.svelte";

    let user_id_value;
</script>

<div class="container">
    <h1>Compotes app, new version!</h1>

    <div class="row">
        <div class="col mb-6">
            <Login/>
        </div>
        <div class="col mb-6">
            <Register/>
        </div>
    </div>
</div>

<style lang="scss">
    h1 {
        text-align: center;
        padding: 1em;
        max-width: 240px;
        margin: 0 auto;
    }
</style>
