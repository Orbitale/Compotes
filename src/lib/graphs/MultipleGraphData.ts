import type GraphData from './GraphData';

export default class MultipleGraphData {
	public static readonly type: string = 'multiple_graph_data';
	public readonly type: string = MultipleGraphData.type;

	public readonly discriminant_display_name: string;
	public readonly graphs: Array<GraphData>;

	constructor(discriminant_display_name: string, graphs: Array<GraphData>) {
		this.discriminant_display_name = discriminant_display_name;
		this.graphs = graphs;
	}
}
