use rusqlite::types::FromSql;
use rusqlite::types::FromSqlError;
use rusqlite::types::FromSqlResult;
use rusqlite::types::ValueRef;
use serde::Serialize;
use serde::Deserialize;

#[derive(Debug, Serialize, Deserialize)]
pub(crate) enum OperationState {
    #[serde(rename = "ok")]
    Ok,
    #[serde(rename = "pending_triage")]
    PendingTriage,
}

impl FromSql for OperationState {
    fn column_result(value: ValueRef<'_>) -> FromSqlResult<Self> {
        let value_as_str = value.as_str().expect("Invalid Non-string value for OperationState fetched from database");
        match value_as_str {
            "ok" => FromSqlResult::Ok(OperationState::Ok),
            "pending_triage" => FromSqlResult::Ok(OperationState::PendingTriage),
            _ => FromSqlResult::Err(FromSqlError::InvalidType)
        }
    }
}
