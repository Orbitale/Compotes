import { toast } from '@zerodevx/svelte-toast';

export enum ToastType {
	info = 'info',
	success = 'success',
	warning = 'warning',
	error = 'error'
}

export default class Toast {
	private readonly _content: string;
	private readonly _toast_type: ToastType;

	constructor(content: string, type: ToastType) {
		this._content = (content || '').replace(/\n/g, '<br>');
		this._toast_type = type;
	}

	show() {
		toast.push({
			msg: this._content,
			theme: this.theme(),
			pausable: true
		});
	}

	private theme(): { [key: string]: string } {
		const style = Toast.getColors(this._toast_type);

		style['--toastWidth'] = '25rem';

		return style;
	}

	static getColors(toast_type: ToastType): { [key: string]: string } {
		switch (toast_type) {
			case ToastType.info:
				return { '--toastBackground': '#6ee0f7', '--toastColor': '' };
			case ToastType.warning:
				return { '--toastBackground': '#f7d56e', '--toastColor': '' };
			case ToastType.error:
				return { '--toastBackground': '#f07582', '--toastColor': '' };
			case ToastType.success:
				return { '--toastBackground': '#79ecb5', '--toastColor': '' };
			default:
				throw 'Invalid toast type.';
		}
	}
}
