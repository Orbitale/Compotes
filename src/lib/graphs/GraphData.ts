import type {GraphDataset} from "./GraphDataset";

export class GraphData {
    // X-axis labels
    public readonly labels: Array<string>;

    public readonly datasets: Array<GraphDataset>;

    constructor(labels: Array<string>, datasets: Array<GraphDataset>) {
        this.labels = labels;
        this.datasets = datasets;
    }
}
