"use strict";

import '../css/draggable.css';
import {Sortable} from '@shopify/draggable';

const sortable = new Sortable(document.querySelectorAll('.draggable_container'), {
    draggable: '.draggable'
});

sortable.on('sortable:stop', (event) => {
    const draggedInputs = document.querySelectorAll('.draggable_container .draggable input');
    draggedInputs.forEach((input: Element, index: number) => {
        if (input.hasAttribute('name')) {
            let name = input.getAttribute('name') || '';
            if (!name.match(/\[\d+]/)) {
                // No DOM modification if attribute was removed already.
                return;
            }
            input.setAttribute('name', name.replace(/\[\d+]/g, '['+index+']'));
        }
    });
});
