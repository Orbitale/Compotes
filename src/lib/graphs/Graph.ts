import type { GraphData } from './GraphData';
import type Operation from '../entities/Operation';

export default interface Graph {
	name(): string;

	getData(): Array<GraphData>;
}

export abstract class AbstractGraph {
	protected readonly operations: Array<Operation>;

	static name(): string;

	public constructor(operations: Array<Operation>) {
		this.operations = operations;
	}
}
