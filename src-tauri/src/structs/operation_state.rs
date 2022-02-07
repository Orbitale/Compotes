use rusqlite::types::FromSql;
use rusqlite::types::FromSqlError;
use rusqlite::types::FromSqlResult;
use rusqlite::types::ToSqlOutput;
use rusqlite::types::ValueRef;
use rusqlite::ToSql;
use serde::Deserialize;
use serde::Serialize;

#[derive(strum_macros::Display, Debug, Serialize, Deserialize)]
pub(crate) enum OperationState {
    #[serde(rename = "ok")]
    Ok,
    #[serde(rename = "pending_triage")]
    PendingTriage,
}

impl FromSql for OperationState {
    fn column_result(value: ValueRef<'_>) -> FromSqlResult<Self> {
        let value_as_str = value
            .as_str()
            .expect("Invalid Non-string value for OperationState fetched from database");
        match value_as_str {
            "ok" => FromSqlResult::Ok(OperationState::Ok),
            "pending_triage" => FromSqlResult::Ok(OperationState::PendingTriage),
            _ => FromSqlResult::Err(FromSqlError::InvalidType),
        }
    }
}

impl ToSql for OperationState {
    fn to_sql(&self) -> rusqlite::Result<ToSqlOutput<'_>> {
        rusqlite::Result::Ok(match self {
            OperationState::Ok => ToSqlOutput::from("ok".to_string()),
            OperationState::PendingTriage => ToSqlOutput::from("pending_triage".to_string()),
        })
    }
}
