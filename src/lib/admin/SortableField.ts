import {OrderBy} from "$lib/admin/OrderBy";

export default class SortableField {
    public readonly name: string;
    public order_by: OrderBy;

    constructor(name: string, order: OrderBy = OrderBy.ASC) {
        this.name = name;
        this.order_by = order;
    }

    get is_asc(): boolean {
        return this.order_by === OrderBy.ASC;
    }

    swapOrder() {
        this.order_by = this.order_by === OrderBy.ASC ? OrderBy.DESC : OrderBy.ASC;
    }
}