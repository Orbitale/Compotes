import FilterWithValue from "./FilterWithValue.ts";

export default class SavedFilter {
    public readonly name: string;
    private readonly filters: string;

    constructor(name: string, filters_with_values: Array<FilterWithValue>) {
        this.name = name;
        this.filters = JSON.stringify(filters_with_values);
    }
}
