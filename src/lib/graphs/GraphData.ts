import {GraphDataset} from "./GraphDataset.ts";

export class GraphData {
    public readonly labels: Array<string>;
    public readonly datasets: Array<GraphDataset>;

    constructor(labels: Array<string>, datasets: Array<GraphDataset>) {
        this.labels = labels;
        this.datasets = datasets;
    }
}
