<script lang="ts">
    import {getTagById, saveTag} from "../../db/tags.ts";
    import type Tag from "../../entities/Tag.ts";
    import {onMount} from "svelte";
    import random_bytes from "../../utils/random.ts";
    import {error, success} from "../../utils/message.ts";
    import {pop} from "svelte-spa-router";

    export let params: {id: string};

    const id = parseInt(params.id, 10);
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

    async function submitForm(e: Event) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        try {
            await saveTag(tag);
        } catch (e) {
            error(e);
            return;
        }

        success('Tag saved!');
        await pop();

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
