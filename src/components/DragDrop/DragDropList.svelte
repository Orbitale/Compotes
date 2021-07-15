<script>
    //
    //
    // Copy/pasted & adapted from https://github.com/jwlarocque/svelte-dragdroplist
    // which is released under ISC license.
    //
    //

    import {flip} from "svelte/animate";

    export let data = [];
    export let removesItems = false;

    let ghost;
    let grabbed;

    let lastTarget;

    let mouseX = 0; // pointer y coordinate within client
    let offsetX = 0; // y distance from top of grabbed element to pointer
    let layerX = 0; // distance from top of list to top of client

    function grab(clientX, element) {
        // modify grabbed element
        grabbed = element;
        grabbed.dataset.grabX = clientX;

        // modify ghost element (which is actually dragged)
        ghost.innerHTML = grabbed.innerHTML;

        // record offset from cursor to top of element
        // (used for positioning ghost)
        offsetX = grabbed.getBoundingClientRect().x - clientX;
        drag(clientX);
    }

    // drag handler updates cursor position
    function drag(clientX) {
        if (grabbed) {
            mouseX = clientX;
            layerX = ghost.parentNode.getBoundingClientRect().x;
        }
    }

    // touchEnter handler emulates the mouseenter event for touch input
    // (more or less)
    function touchEnter(ev) {
        drag(ev.clientX);
        // trigger dragEnter the first time the cursor moves over a list item
        let target = document.elementFromPoint(ev.clientX, ev.clientY).closest(".item");
        if (target && target !== lastTarget) {
            lastTarget = target;
            dragEnter(ev, target);
        }
    }

    function dragEnter(ev, target) {
        // swap items in data
        if (grabbed && target !== grabbed && target.classList.contains("item")) {
            moveDatum(parseInt(grabbed.dataset.index), parseInt(target.dataset.index));
        }
    }

    // does the actual moving of items in data
    function moveDatum(from, to) {
        let temp = data[from];
        data = [...data.slice(0, from), ...data.slice(from + 1)];
        data = [...data.slice(0, to), temp, ...data.slice(to)];
    }

    function release() {
        grabbed = null;
    }

    function removeDatum(index) {
        data = [...data.slice(0, index), ...data.slice(index + 1)];
    }
</script>

<style lang="scss">
    main {
        position: relative;
    }

    .list {
        z-index: 5;
        display: flex;
        flex-direction: row;

        .item {
            cursor: grab;
            box-sizing: border-box;
            display: inline-flex;
            min-height: 2em;
            background-color: white;
            border: 1px solid rgb(190, 190, 190);
            border-radius: 2px;
            user-select: none;
            .content {
                padding-left: 1em;
                padding-right: 1em;
            }
            &:last-child {
                margin-bottom: 0;
            }
            #grabbed {
                cursor: grabbing;
            }
            &:not(#grabbed):not(#ghost) {
                z-index: 10;
            }
            & > * {
                margin: auto;
            }
        }
    }

    .delete {
        width: 32px;
    }

    #grabbed {
        opacity: 0.0;
        cursor: grabbing;
    }

    #ghost {
        pointer-events: none;
        z-index: -5;
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0.0;
        &.haunting {
            z-index: 20;
            opacity: 1.0;
        }
        * {
            pointer-events: none;
        }
    }
</style>

<!-- All the documentation has to go up here, sorry.
     (otherwise it conflicts with the HTML or svelte/animate)
     The .list has handlers for pointer movement and pointer up/release/end.
     Each .item has a handler for pointer down/click/start, which assigns that
     element as the item currently being "grabbed".  They also have a handler
     for pointer enter (the touchmove handler has extra logic to behave like the
     no longer extant 'touchenter'), which swaps the entered element with the
     grabbed element when triggered.
     You'll also find reactive styling below, which keeps it from being directly
     part of the imperative javascript handlers. -->
<main class="dragdroplist">
    <div
        bind:this={ghost}
        id="ghost"
        class={grabbed ? "item haunting" : "item"}
        style={"left: " + (mouseX + offsetX - layerX) + "px"}><p></p></div>
    <div
        class="list"
        on:mousemove={function(ev) {ev.stopPropagation(); drag(ev.clientX);}}
        on:touchmove={function(ev) {ev.stopPropagation(); drag(ev.touches[0].clientX);}}
        on:mouseup={function(ev) {ev.stopPropagation(); release(ev);}}
        on:touchend={function(ev) {ev.stopPropagation(); release(ev.touches[0]);}}>
        {#each data as datum, i (datum.id ? datum.id : JSON.stringify(datum))}
            <div
                id={(grabbed && (datum.id ? datum.id : JSON.stringify(datum)) == grabbed.dataset.id) ? "grabbed" : ""}
                class="item"
                data-index={i}
                data-id={(datum.id ? datum.id : JSON.stringify(datum))}
                data-grabX="0"
                on:mousedown={function(ev) {grab(ev.clientX, this);}}
                on:touchstart={function(ev) {grab(ev.touches[0].clientX, this);}}
                on:mouseenter={function(ev) {ev.stopPropagation(); dragEnter(ev, ev.target);}}
                on:touchmove={function(ev) {ev.stopPropagation(); ev.preventDefault(); touchEnter(ev.touches[0]);}}
                animate:flip|local={{duration: 200}}>

                <div class="content">
                    {#if datum.html}
                        {@html datum.html}
                    {:else if datum.text}
                        <p>{datum.text}</p>
                    {:else}
                        <p>{datum}</p>
                    {/if}
                </div>

                {#if removesItems}
                    <div class="buttons delete">
                        <button
                            on:click={function(ev) {removeDatum(i);}}>
                            ✖
                        </button>
                    </div>
                {/if}
            </div>
        {/each}
    </div>
</main>