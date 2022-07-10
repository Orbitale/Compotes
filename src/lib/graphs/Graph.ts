import type GraphData from './GraphData';
import type MultipleGraphData from "./MultipleGraphData";

export default interface Graph {
	getData(): GraphData|MultipleGraphData;
}
