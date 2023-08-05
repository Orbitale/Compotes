import { AbstractOperationGraph } from './AbstractOperationGraph';
import GraphData from '../graphs/GraphData';
import GraphDataset from '../graphs/GraphDataset';
import { DateTime } from '../utils/date';

export default class MonthlyTotals extends AbstractOperationGraph {
	public static getName(): string {
		return 'Monthly Totals';
	}

	getData(): GraphData {
		let series = {};

		for (const operation of this.operations) {
			const year = operation.dateObject.year;
			if (!series[year]) {
				series[year] = {
					name: year,
					months: {}
				};
			}
			const month = operation.dateObject.month;
			if (!series[year].months[month]) {
				series[year].months[month] = 0;
			}
			series[year].months[month] += operation.amount;
		}

		for (const year in series) {
			const serie = series[year];
			for (let i = 1; i <= 12; i++) {
				let index = i.toString();
				if (!serie.months[index]) {
					serie.months[index] = null;
				}
			}
		}

		let datasets = [];

		let i = 0;
		let number_of_years = Object.keys(series).length;

		for (const year in series) {
			const hue = (i / number_of_years) * 255;
			i++;
			const dataset = new GraphDataset(
				`Year ${year}`,
				Object.values(series[year].months),
				`hsl(${hue}, 100%, 50%)`,
				`hsl(${hue}, 100%, 50%)`,
				1
			);
			datasets.push(dataset);
		}

		return new GraphData(
			MonthlyTotals.getName(),
			Array.from(Array(12).keys()).map((i) => {
				const month_number = (i + 1).toString();
				return DateTime.now().set({ month: month_number }).toFormat('MMMM');
			}),
			datasets
		);
	}
}
