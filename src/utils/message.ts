import Toast, {ToastType} from '../struct/Toast';
//import api_call from "./api_call";

let container: HTMLElement;

function getContainerElement(): HTMLElement {
    if (container) {
        return container;
    }

    const element = document.getElementById('notifications-container');

    if (!(element instanceof HTMLElement)) {
        throw 'Notifications container seems to be absent from the DOM';
    }

    container = element;

    return container;
}

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

export default function message(content: string, type: ToastType) {
    container = getContainerElement();

    if (!type) {
        type = ToastType.info;
    }

    const toast = new Toast(content, container, type);

    toast.show();

    return toast;
};
