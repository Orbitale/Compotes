import FilterWithValue from "./FilterWithValue.ts";

export default class SavedFilter {
    public readonly name: string;
    public readonly deserialized_filters: Array<FilterWithValue>;
    private readonly filters: string;

    constructor(name: string, filters_with_values: Array<FilterWithValue>) {
        this.name = name;
        this.deserialized_filters = filters_with_values;
        this.filters = JSON.stringify(filters_with_values);
    }

    public static fromSerialized(name: string, filters: string): SavedFilter {
        let deserialized_filters: Array<FilterWithValue> = JSON.parse(filters);

        if (!Array.isArray(deserialized_filters)) {
            console.error('Stored filters are not stored as an array. Resetting them.');
            deserialized_filters = [];
        }

        deserialized_filters = deserialized_filters.map((f: filter) => FilterWithValue.fromSerialized(f));

        return new SavedFilter(name, deserialized_filters);
    }
}
