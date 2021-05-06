use serde::Serialize;
use serde::Deserialize;
use crate::structs::operation_state::OperationState;

#[derive(Serialize, Deserialize)]
pub(crate) struct Operation
{
    pub(crate) id: u32,
    pub(crate) operation_date: String,
    pub(crate) op_type: String,
    pub(crate) type_display: String,
    pub(crate) details: String,
    pub(crate) amount_in_cents: i32,
    pub(crate) hash: String,
    pub(crate) state: OperationState,
    pub(crate) ignored_from_charts: bool,
    pub(crate) bank_account_id: u32,
}
