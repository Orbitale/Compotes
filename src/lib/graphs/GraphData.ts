import type GraphDataset from './GraphDataset';

class GraphData {
	public static readonly type: string = 'graph_data';
	public readonly type: string = GraphData.type;

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
