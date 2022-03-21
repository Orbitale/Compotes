export enum OrderBy {
    ASC = 'ASC',
    DESC = 'DESC',
}

export function orderByToString(order_by: OrderBy): string {
    switch (order_by) {
        case OrderBy.ASC: return 'ASC';
        case OrderBy.DESC: return 'DESC';
        default: throw new Error(`Unsupported order_by type: ${order_by}`);
    }
}
