<script lang="ts">
    import {saveTagRule} from "../../db/tag_rules.ts";
    import type tag_rule from "../../entities/TagRule.ts";
    import random_bytes from "../../utils/random.ts";
    import {error, success} from "../../utils/message.ts";
    import {pop} from "svelte-spa-router";
    import TagRule from "../../entities/TagRule.ts";

    let tag_rule: TagRule = TagRule.empty();
    let submit_button_disabled: boolean = false;

    function onNameChange() {
        submit_button_disabled = tag_rule.tags.length === 0;
    }

    async function submitForm(e: Event) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        try {
            await saveTagRule(tag_rule);
        } catch (e) {
            error(e);
            return;
        }

        success('Bank account saved!');
        await pop();

        return false;
    }

    const rand = '_'+random_bytes(20);

</script>

<form action="#" on:submit={submitForm} >

    <h2>Create bank account</h2>

    <div class="row">
        <label for="name{rand}" class="col-form-label col-sm-2">
            Name
        </label>
        <div class="col-sm-10">
            <input autocomplete="{rand}" type="text" id="name{rand}" bind:value={tag_rule.name} on:keyup={onNameChange} class="form-control">
        </div>
    </div>

    <div class="row">
        <label for="is_regex{rand}" class="col-form-label col-sm-2">
            Regular expression
        </label>
        <div class="col-sm-10">
            <input type="checkbox" id="is_regex{rand}" bind:value={tag_rule.is_regex} class="form-control">
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
