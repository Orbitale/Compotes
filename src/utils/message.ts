import Toast from '../struct/Toast';

function createContainerElement(): HTMLElement {
    const html = '<div class="toast-container position-absolute p-3 top-0 end-0"></div>';

    const div = document.createElement('div');

    div.innerHTML = html;

    const container = div.firstChild;

    if (!(container instanceof HTMLElement)) {
        throw 'Toast container could not be created successfully.';
    }

    return container;
}

let container;
let addedToBody = false;

export default function message(content: string) {
    if (!addedToBody) {
        container = createContainerElement();
        document.body.appendChild(container);
        addedToBody = true;
    }

    const toast = new Toast(content, container, bootstrap.Toast);
    toast.show();
};
