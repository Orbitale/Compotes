
use rusqlite::Connection;
use rusqlite::NO_PARAMS;
use serde::Serialize;
use serde::Deserialize;
use serde_rusqlite::from_rows;
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

pub(crate) fn find_all(conn: &Connection) -> Vec<Operation>
{
    let mut stmt = conn.prepare("
        SELECT
            id,
            operation_date,
            type as op_type,
            type_display,
            details,
            amount_in_cents,
            hash,
            state,
            bank_account_id,
            ignored_from_charts
        FROM operations
    ").expect("Could not fetch operations");

    let mut operations: Vec<Operation> = Vec::new();

    let mut rows_iter = from_rows::<Operation>(stmt.query(NO_PARAMS).unwrap());

    loop {
        match rows_iter.next() {
            None => { break; },
            Some(operation) => {
                let operation = operation.expect("Could not deserialize Operation item");
                operations.push(operation);
            }
        }
    }

    operations
}