import {GraphData} from "./GraphData.ts";
import Operation from "../entities/Operation.ts";

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
