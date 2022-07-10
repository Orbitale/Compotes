import type Graph from "./Graph";
import type GraphData from "./GraphData";
import type MultipleGraphData from "./MultipleGraphData";

export default abstract class AbstractGraph implements Graph {
	public static getName(): string {
		throw new Error('OperationGraph.name() must be implemented.');
	}

	abstract getData(): GraphData | MultipleGraphData;
}
