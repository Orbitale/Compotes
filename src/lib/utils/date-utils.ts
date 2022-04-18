import * as baseDayJs from 'dayjs/esm/index.js';
import localeData from 'dayjs/esm/plugin/localeData/index.js';
import minMax from 'dayjs/esm/plugin/minMax/index.js';
import isSameOrBefore from 'dayjs/esm/plugin/isSameOrBefore/index.js';
import isSameOrAfter from 'dayjs/esm/plugin/isSameOrAfter/index.js';

let dayjs = baseDayJs;

if (baseDayJs.default) {
    dayjs = baseDayJs.default;
}

if (dayjs && dayjs.extend) {
  dayjs.extend(localeData);
  dayjs.extend(minMax);
  dayjs.extend(isSameOrBefore);
  dayjs.extend(isSameOrAfter);
} else if (!dayjs) {
  console.error('"dayjs" import is empty.');
} else if (!dayjs.extend) {
  console.error('"dayjs" import does not implement "extend".');
}

export {
    dayjs
};
