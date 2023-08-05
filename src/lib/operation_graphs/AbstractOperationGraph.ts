import type Operation from '../entities/Operation';
import AbstractGraph from '../graphs/AbstractGraph';

export abstract class AbstractOperationGraph extends AbstractGraph {
	protected readonly operations: Array<Operation>;

	constructor(operations: Array<Operation>) {
		super();
		this.operations = operations;
	}
}
