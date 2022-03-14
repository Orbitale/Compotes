use rusqlite::types::FromSql;
use rusqlite::types::FromSqlError;
use rusqlite::types::FromSqlResult;
use rusqlite::types::ToSqlOutput;
use rusqlite::types::ValueRef;
use rusqlite::ToSql;
use serde::Deserialize;
use serde::Serialize;
use std::fmt::Display;
use std::fmt::Formatter;

#[derive(Debug, Serialize, Deserialize)]
pub(crate) enum OperationState {
    #[serde(rename = "ok")]
    Ok,
    #[serde(rename = "pending_triage")]
    PendingTriage,
}

impl Display for OperationState {
    fn fmt(&self, f: &mut Formatter<'_>) -> std::fmt::Result {
        write!(
            f,
            "{}",
            match self {
                OperationState::Ok => "ok".to_string(),
                OperationState::PendingTriage => "pending_triage".to_string(),
            }
        )
    }
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
        rusqlite::Result::Ok(ToSqlOutput::from(self.to_string()))
    }
}

#[cfg(test)]
mod tests {
    use crate::structs::operation_state::OperationState;

    #[test]
    fn ok_to_string() {
        assert_eq!(OperationState::Ok.to_string(), "ok".to_string());
    }

    #[test]
    fn pending_triage_to_string() {
        assert_eq!(
            OperationState::PendingTriage.to_string(),
            "pending_triage".to_string()
        );
    }
}
