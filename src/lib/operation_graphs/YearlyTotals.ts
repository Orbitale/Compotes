import {AbstractOperationGraph} from "./AbstractOperationGraph";
import GraphData from "../graphs/GraphData";
import GraphDataset from "../graphs/GraphDataset";

export default class YearlyTotals extends AbstractOperationGraph {
	public static getName(): string {
		return 'Yearly totals';
	}

	getData(): GraphData {
		let series_years = [];
		let data = [];

		for (const operation of this.operations) {
			const year = operation.dateObject.year;
			if (!series_years[year]) {
				series_years[year] = year;
			}
			const current_series_index = series_years.indexOf(year);
			if (!data[current_series_index]) {
				data[current_series_index] = 0;
			}
			data[current_series_index] += operation.amount;
		}

		return new GraphData(
			YearlyTotals.getName(),
			series_years.filter((i) => !!i).map((i) => i.toString()),
			[
				new GraphDataset(
					'Amount',
					data.filter((i) => !!i),
					'rgba(255, 99, 132, 0.2)',
					'rgba(255, 99, 132, 1)',
					1
				)
			]
		);
	}
}
