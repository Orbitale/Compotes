"use strict";

const dateRangeInputId = 'analytics_filter_dateRange';
const startDateInputId = 'analytics_filter_startDate';
const endDateInputId = 'analytics_filter_endDate';

const dateRangeInput = getSelect(dateRangeInputId);

if (dateRangeInput) {
    const startDateInput = getInput(startDateInputId);
    const endDateInput = getInput(endDateInputId);

    if (!startDateInput) {
        throw new Error('Cannot find start date input.');
    }
    if (!endDateInput) {
        throw new Error('Cannot find end date input.');
    }

    dateRangeInput.addEventListener('change', () => {
        updateDateFields(dateRangeInput, startDateInput, endDateInput);
    });

    updateDateFields(dateRangeInput, startDateInput, endDateInput);
}

function updateDateFields(
    dateRangeInput: HTMLSelectElement,
    startDateInput: HTMLInputElement,
    endDateInput: HTMLInputElement
): void {
    if (dateRangeInput.value) {
        startDateInput.setAttribute('disabled', 'disabled');
        startDateInput.value = '';
        endDateInput.setAttribute('disabled', 'disabled');
        endDateInput.value = '';
    } else {
        startDateInput.removeAttribute('disabled');
        endDateInput.removeAttribute('disabled');
    }
}

function getInput(id: string): HTMLInputElement
{
    const element = document.getElementById(id);

    if (!(element instanceof HTMLInputElement)) {
        console.error(element);
        throw new Error(`Element with id "${id}" was expected to be an input.`);
    }

    return element;
}

function getSelect(id: string): null|HTMLSelectElement
{
    const element = document.getElementById(id);

    if (element && !(element instanceof HTMLSelectElement)) {
        console.error(element);
        throw new Error(`Element with id "${id}" was expected to be a select.`);
    }

    return element;
}
