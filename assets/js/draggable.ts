"use strict";

import '../css/draggable.css';
import {Sortable} from '@shopify/draggable';

const draggableContainerClass = 'draggable_container';
const draggableClass = 'draggable';

if (document.getElementsByClassName(draggableContainerClass).length) {
    const sortable = new Sortable(document.querySelectorAll(`.${draggableContainerClass}`), {
        draggable: `.${draggableClass}`
    });

    sortable.on('sortable:stop', () => {
        document
            .querySelectorAll(`.${draggableContainerClass} .${draggableClass} input`)
            .forEach((input: Element, index: number) => {
                if (input.hasAttribute('name')) {
                    let name = input.getAttribute('name') || '';
                    if (!name.match(/\[\d+]/)) {
                        // No DOM modification if attribute was already removed.
                        return;
                    }
                    input.setAttribute('name', name.replace(/\[\d+]/g, '['+index+']'));
                }
            })
        ;
    });
}
