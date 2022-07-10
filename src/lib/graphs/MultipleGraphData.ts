import type GraphData from "./GraphData";

export default class MultipleGraphData {
	public readonly discriminant_display_name: string;
	public readonly graphs: Array<GraphData>;

	constructor(discriminant_display_name: string, graphs: Array<GraphData>) {
		this.discriminant_display_name = discriminant_display_name;
		this.graphs = graphs;
	}
}
