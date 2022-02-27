import {toast} from "@zerodevx/svelte-toast";

export enum ToastType {
    info = 'info',
    success = 'success',
    warning = 'warning',
    error = 'error',
}

export default class Toast {
    private readonly _content: string;
    private readonly _toast_type: ToastType;

    constructor(content: string, type: ToastType) {
        this._content = (content || '').replace("\n", "<br>");
        this._toast_type = type;
    }

    show() {
        toast.push({
            msg: this._content,
            theme: this.theme(),
        });
    }

    private theme() {
        switch (this._toast_type) {
            case ToastType.info: return {'--toastBackground': '#0dcaf0', '--toastColor': ''};
            case ToastType.warning: return {'--toastBackground': '#ffc107', '--toastColor': ''};
            case ToastType.error: return {'--toastBackground': '#dc3545', '--toastColor': ''};
            case ToastType.success: return {'--toastBackground': '#198754', '--toastColor': ''};
            default: throw 'Invalid toast type.';
        }
    }
}