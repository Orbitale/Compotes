import type GraphDataset from './GraphDataset';

class GraphData {
	// X-axis labels
	public readonly name: string;

	public readonly labels: Array<string>;

	public readonly datasets: Array<GraphDataset>;

	constructor(name: string, labels: Array<string>, datasets: Array<GraphDataset>) {
		this.name = name;
		this.labels = labels;
		this.datasets = datasets;
	}
}

export default GraphData;
