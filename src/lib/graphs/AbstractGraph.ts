import type GraphData from "./GraphData";
import type MultipleGraphData from "./MultipleGraphData";

export default abstract class AbstractGraph {
	public static getName(): string {
		throw new Error('OperationGraph.name() must be implemented.');
	}

	abstract getData(): GraphData | MultipleGraphData;
}
