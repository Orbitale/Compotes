import { DateTime } from 'luxon';

export { DateTime };

export enum DateFormat {
	DMY_SLASH = 'D/M/Y',
	YMD_SLASH = 'Y/M/D',
	YDM_SLASH = 'Y/D/M',
	MDY_SLASH = 'M/D/Y',
	YMD_DASH = 'Y-M-D',
	YDM_DASH = 'Y-D-M',
	DMY_DASH = 'D-M-Y',
	MDY_DASH = 'M-D-Y'
}

export function dateFormatToRegex(format: DateFormat): RegExp {
	switch (format) {
		case DateFormat.YMD_SLASH:
			return new RegExp('^(?<year>\\d+)/(?<month>\\d+)/(?<day>\\d+)$');
		case DateFormat.YDM_SLASH:
			return new RegExp('^(?<year>\\d+)/(?<day>\\d+)/(?<month>\\d+)$');
		case DateFormat.DMY_SLASH:
			return new RegExp('^(?<day>\\d+)/(?<month>\\d+)/(?<year>\\d+)$');
		case DateFormat.MDY_SLASH:
			return new RegExp('^(?<month>\\d+)/(?<day>\\d+)/(?<year>\\d+)$');
		case DateFormat.YMD_DASH:
			return new RegExp('^(?<year>\\d+)-(?<month>\\d+)-(?<day>\\d+)$');
		case DateFormat.YDM_DASH:
			return new RegExp('^(?<year>\\d+)-(?<day>\\d+)-(?<month>\\d+)$');
		case DateFormat.DMY_DASH:
			return new RegExp('^(?<day>\\d+)-(?<month>\\d+)-(?<year>\\d+)$');
		case DateFormat.MDY_DASH:
			return new RegExp('^(?<month>\\d+)-(?<day>\\d+)-(?<year>\\d+)$');
		default:
			throw `Unsupported date format "${format}".`;
	}
}

export class NormalizedDate extends Date {
	private readonly _date: Date;

	constructor(normalizedDate: Date) {
		super();
		this._date = normalizedDate;
	}

	public toString() {
		return this._date.toISOString();
	}
}
