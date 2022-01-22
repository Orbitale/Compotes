import Toast, {ToastType} from '../struct/Toast';
import api_call from "./api_call";

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

let container: HTMLElement;
let addedToBody = false;

export function success(content: string): Toast {
    return message(content, ToastType.success);
}

export function error(content: string): Toast {
    return message(content, ToastType.error);
}

export function warning(content: string): Toast {
    return message(content, ToastType.warning);
}

export function info(content: string): Toast {
    return message(content, ToastType.info);
}

export default function message(content: string, type: ToastType): Toast {
    api_call("message", {
        id: "compotes.notification",
        title: type,
        message: content
    });

    /* Info: keeping this code in case we need to fall back to app notifications instead of desktop notifications.
    if (!addedToBody) {
        container = createContainerElement();
        document.body.appendChild(container);
        addedToBody = true;
    }

    if (!type) {
        type = ToastType.info;
    }

    const toast = new Toast(content, container, type);

    toast.show();

    return toast;
    */
};
