import {Toast as BootstrapToast} from 'bootstrap';

export enum ToastType {
    info = 'info',
    success = 'success',
    warning = 'warning',
    error = 'error',
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
        this._toastElement = Toast.createToastElement(content, type);
        containerElement.appendChild(this._toastElement);
        this._bootstrapToast = new BootstrapToast(this._toastElement);
    }

    show() {
        this._bootstrapToast.show();
    }

    hide() {
        this._bootstrapToast.hide();
    }

    private static createToastElement(content: string, toastType: ToastType): HTMLElement {
        const html =
            '<div class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true">' +
            `    <div class="d-flex ${Toast.toastTypeToClassName(toastType)}">` +
            '        <div class="toast-body">' +
            '            ' + content +
            '        </div>' +
            '        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '    </div>' +
            '</div>'
        ;

        const div = document.createElement('div');

        div.innerHTML = html;

        const toast = div.firstChild;

        if (!(toast instanceof HTMLElement)) {
            throw 'Toast element could not be created.';
        }

        return toast;
    }

    private static toastTypeToClassName(type: ToastType) {
        switch (type) {
            case ToastType.info: return 'alert-info';
            case ToastType.warning: return 'alert-warning';
            case ToastType.error: return 'alert-danger';
            case ToastType.success: return 'alert-success';
            default: throw 'Invalid toast type.';
        }
    }

    private static toastTypeToMessageTitle(type: ToastType) {
        switch (type) {
            case ToastType.info: return 'ℹ Compotes info';
            case ToastType.success: return '✅ Compotes';
            case ToastType.warning: return '⚠ Compotes warning';
            case ToastType.error: return '❌ Compotes alert';
            default: throw 'Invalid toast type.';
        }
    }
}