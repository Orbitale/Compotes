"use strict";

const buttons_selector = 'button.operation-add-tags[data-operation-id]';
const modal_id = 'operation-tags-modal';
const modal_form_selector = 'form[name="operation_tags"]';

let buttons = document.querySelectorAll(buttons_selector);
let modal = document.getElementById(modal_id);
let modal_form = modal ? document.querySelector(modal_form_selector) : null;
let tags_select = modal_form ? modal_form.querySelector('select') : null;

(async function(){
    if (!buttons.length || !modal || !modal_form || !tags_select) {
        return;
    }

    let selected_id: number;

    buttons.forEach(function (button: Element) {
        button.addEventListener('click', plusButtonClick, {
            capture: true,
            passive: true,
        });
    });

    modal_form.addEventListener('submit', onTagFormSubmit);

    function plusButtonClick(e: Event) {
        if (!(e.target instanceof HTMLButtonElement)) {
            console.error('Invalid button element.');
            return;
        }

        // @ts-ignore
        $(tags_select).val(null).trigger('change');

        selected_id = parseInt(<string>e.target.getAttribute('data-operation-id'), 10);
    }


    function onTagFormSubmit(e: Event) {
        e.preventDefault();
        e.stopPropagation();

        if (!selected_id) {
            return;
        }

        if (isNaN(selected_id)) throw 'Invalid operation ID, expected a number.';
        if (!(tags_select instanceof HTMLSelectElement)) throw 'Invalid tags <select> tag.';

        let options: HTMLOptionsCollection = tags_select.options;
        let selected_tags: Array<string> = [];
        for (const key in options) {
            if (!options.hasOwnProperty(key)) continue;
            let option: HTMLOptionElement = options[key];
            if (option.selected) {
                selected_tags.push(option.value);
            }
        }

        fetch('/admin/api/operation/'+selected_id+'/tags', {
            method: 'post',
            body: JSON.stringify(selected_tags),
            headers: {
                Accept: 'application/json',
            }
        })
            .then((res) => res.json())
            .then(onOperationTagsSave(selected_id))
            .catch((e) => {
                console.error('Could not add new tags to operation.');
                throw e;
            })
        ;
    }


    function onOperationTagsSave(current_id: number) {
        return function (json: any) {
            const tags_html = json.tags_html;
            if (!tags_html) throw 'Invalid JSON response: missing "tags_html" field.';

            // @ts-ignore
            $(modal).modal('hide');

            let triggered_button = document.querySelector(buttons_selector + '[data-operation-id="' + current_id + '"]')

            if (!(triggered_button instanceof HTMLButtonElement)) {
                throw 'Could not find the button that was triggered for operation with id "' + current_id + '".';
            }

            triggered_button.insertAdjacentHTML('afterend', tags_html);

            return true;
        };
    }
})();
