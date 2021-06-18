<script lang="ts">
    import {getTagRuleById, saveTagRule} from "../../db/tag_rules.ts";
    import type TagRule from "../../entities/TagRule.ts";
    import {onMount} from "svelte";
    import {success, error} from "../../utils/message.ts";
    import {pop} from "svelte-spa-router";

    export let params: {id: string};

    const id = params.id;
    let tag_rule: TagRule;
    let submit_button_disabled: boolean = false;

    onMount(async () => {
        const fetched_tag = await getTagRuleById(id);
        if (!fetched_tag) {
            throw `TagRule with ID "${id}" does not exist.`;
        }
        tag_rule = fetched_tag.clone();
    });

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

        success('Tag rule saved!');
        await pop();
    }

</script>

<style lang="scss">
    textarea {
        min-height: 100px;
    }
    .checkbox-container {
        position: relative;
        input[type="checkbox"] {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }
    }
</style>

{#if !tag_rule}
    <div class="alert alert-danger">
        Invalid or inexistent tag rule.
    </div>
{:else}
    <form action="#" on:submit={submitForm} >

        <h2>Edit tag</h2>

        <div class="row">
            <label for="matching_pattern" class="col-form-label col-sm-2">
                Matching pattern
            </label>
            <div class="col-sm-10">
                <textarea type="text" id="matching_pattern" bind:value={tag_rule.matching_pattern} class="form-control"></textarea>
            </div>
        </div>

        <div class="row">
            <label for="is_regex" class="col-form-label col-sm-2">
                Is it a regular expression?
            </label>
            <div class="col-sm-10 clearfix checkbox-container">
                <input type="checkbox" id="is_regex" bind:checked={tag_rule.is_regex} class="form-check-input">
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
