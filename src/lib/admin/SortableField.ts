import {OrderBy} from "$lib/admin/OrderBy";

export default class SortableField {
    public readonly name: string;
    public readonly property_name: string;
    public order_by: OrderBy;

    constructor(name: string, order: OrderBy = OrderBy.ASC, property_name: string) {
        this.name = name;
        this.order_by = order;
        this.property_name = property_name;
    }

    get is_asc(): boolean {
        return this.order_by === OrderBy.ASC;
    }

    swapOrder() {
        this.order_by = this.order_by === OrderBy.ASC ? OrderBy.DESC : OrderBy.ASC;
    }
}