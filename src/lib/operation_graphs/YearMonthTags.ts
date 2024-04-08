import { AbstractOperationGraph } from './AbstractOperationGraph';
import GraphData from '../graphs/GraphData';
import MultipleGraphData from '../graphs/MultipleGraphData';
import { DateTime } from '$lib/utils/date';
import GraphDataset from '$lib/graphs/GraphDataset';

type SeriesType = {
	[key: string]: {
		name: string;
		tags: {
			[key: string]: {
				name: string;
				months: { [key: string]: number };
			};
		};
	};
};

export default class YearMonthTags extends AbstractOperationGraph {
	public static getName(): string {
		return 'Yearly & monthly tag amounts';
	}

	getData(): GraphData | MultipleGraphData {
		let full_series: SeriesType = {};

		for (const operation of this.operations) {
			const year = operation.dateObject.year.toString();
			if (!full_series[year]) {
				full_series[year] = {
					name: year,
					tags: {}
				};
			}

			for (const tag of operation.tags) {
				let tag_name = tag.name;
				if (!full_series[year].tags[tag_name]) {
					full_series[year].tags[tag_name] = {
						name: tag_name,
						months: getEmptyMonthlyData()
					};
				}

				const month = operation.dateObject.month.toString();
				if (!full_series[year].tags[tag_name].months[month]) {
					full_series[year].tags[tag_name].months[month] = 0;
				}

				full_series[year].tags[tag_name].months[month] += Number(operation.amount);
			}
		}

		const graphs: Array<GraphData> = [];

		for (const year in full_series) {
			const datasets = [];
			let i = 0;
			let number_of_series = Object.keys(full_series[year].tags).length;
			full_series[year].tags = sortByKeys(full_series[year].tags);

			for (const tag in full_series[year].tags) {
				const hue = (i++ / number_of_series) * 255;
				const dataset = new GraphDataset(
					tag,
					Object.values(full_series[year].tags[tag].months),
					`hsl(${hue}, 80%, 50%)`,
					`hsl(${hue}, 60%, 50%)`,
					1
				);
				datasets.push(dataset);
			}

			graphs.push(new GraphData(year, getMonthsAsLabels(), datasets));
		}

		return new MultipleGraphData('year', graphs);
	}
}

function sortByKeys(object: { [key: string]: any }): { [key: string]: any } {
	const unsortedObjArr = [...Object.entries(object)];

	const sortedObjArr = unsortedObjArr.sort(([key1, value1], [key2, value2]) =>
		key1.localeCompare(key2)
	);

	const sortedObject = {};

	sortedObjArr.forEach(([key, value]) => (sortedObject[key] = value));

	return sortedObject;
}

function getMonthsAsLabels(): Array<string> {
	return Array.from(Array(12).keys()).map((i) => {
		const month_number = (i + 1).toString();
		return DateTime.now().set({ month: month_number }).toFormat('MMMM');
	});
}

function getEmptyMonthlyData(): { [key: string]: number } {
	const data = {};

	for (let i = 1; i <= 12; i++) {
		data[i.toString()] = 0;
	}

	return data;
}
