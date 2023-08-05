import Toast, { ToastType } from '../struct/Toast';

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
	if (!type) {
		type = ToastType.info;
	}

	const toast = new Toast(content, type);

	toast.show();

	return toast;
}
