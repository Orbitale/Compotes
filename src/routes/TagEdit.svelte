<script lang="ts">
    import {getTagById, saveTag} from "../db/tags.ts";
    import type Tag from "../entities/Tag.ts";
    import {onMount} from "svelte";
    import random_bytes from "../utils/random.ts";

    export let params: {id: string};

    const id = params.id;
    let tag: Tag;
    let submit_button_disabled: boolean = false;

    onMount(async () => {
        const fetched_tag = await getTagById(id);
        if (!fetched_tag) {
            throw `Tag with ID "${id}" does not exist.`;
        }
        tag = fetched_tag.clone();
    });

    function onNameChange() {
        submit_button_disabled = !tag.name;
    }

    function submitForm(e: Event) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        saveTag(tag).catch(function() {
            console.error('NOT OK', arguments);
        });

        return false;
    }

    const rand = '_'+random_bytes(20);

</script>

{#if !tag}
    <div class="alert alert-danger">
        Invalid or inexistent tag.
    </div>
{:else}
    <form action="#" on:submit={submitForm} >

        <h2>Edit tag</h2>

        <div class="row">
            <label for="name{rand}" class="col-form-label col-sm-2">
                Name
            </label>
            <div class="col-sm-10">
                <input autocomplete="{rand}" type="text" id="name{rand}" bind:value={tag.name} on:keyup={onNameChange} class="form-control">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-sm-2">&nbsp;</div>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-primary {submit_button_disabled ? 'disabled' : ''}" disabled={submit_button_disabled}>
                    Save
                </button>
            </div>
        </div>
    </form>
{/if}
