import {Toast as BootstrapToast} from 'bootstrap';

export enum ToastType {
    info = 'ℹ Compotes info',
    success = '✅ Compotes',
    warning = '⚠ Compotes warning',
    error = '❌ Compotes alert',
}

export default class Toast {
    private readonly _content: string;
    private readonly _containerElement: HTMLElement;
    private readonly _toastElement: HTMLElement;
    private readonly _bootstrapToast: BootstrapToast;
    private readonly _toast_type: ToastType;

    constructor(content: string, containerElement: HTMLElement, type: ToastType) {
        this._content = content;

        this._containerElement = containerElement;
        this._toast_type = type;
        this._toastElement = this.createToastElement();
        this._bootstrapToast = new BootstrapToast(this._toastElement);
    }

    show() {
        this._containerElement.appendChild(this._toastElement);

        this._toastElement.classList.add('show');
        this._bootstrapToast.show();
    }

    hide() {
        // this._toastElement.classList.remove('show');
        this._bootstrapToast.hide();
    }

    private createToastElement(): HTMLElement {
        const html =
            '<div class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true">' +
            `    <div class="d-flex ${this._toast_type}">` +
            '        <div class="toast-body">' +
            '            ' + this._content +
            '        </div>' +
            '        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '    </div>' +
            '</div>'
        ;

        const div = document.createElement('div');

        div.innerHTML = html;

        const toast = div.firstChild;

        if (!(toast instanceof HTMLElement)) {
            throw 'Toast element could not be created successfully.';
        }

        return toast;
    }
}