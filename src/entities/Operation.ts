enum OperationState {
    ok = "ok",
    pending_triage = "pending_triage",
}

export default class Operation
{
    public readonly id: number;
    public readonly operation_date: String;
    public readonly op_type: String;
    public readonly type_display: String;
    public readonly details: String;
    public readonly amount_in_cents: number;
    public readonly hash: String;
    public readonly state: OperationState;
    public readonly ignored_from_charts: boolean;
    public readonly bank_account_id: number;
}
