use rusqlite::types::FromSql;
use rusqlite::types::FromSqlError;
use rusqlite::types::FromSqlResult;
use rusqlite::types::ToSqlOutput;
use rusqlite::types::ValueRef;
use std::fmt::Display;
use std::fmt::Formatter;
use serde::Deserialize;
use rusqlite::ToSql;

#[derive(Debug, Copy, Clone, Deserialize)]
pub enum FilterType {
    #[serde(rename = "text")]
    Text,
    #[serde(rename = "date")]
    Date,
    #[serde(rename = "number")]
    Number,
    #[serde(rename = "association")]
    Association,
}

impl Display for FilterType {
    fn fmt(&self, f: &mut Formatter<'_>) -> std::fmt::Result {
        write!(
            f,
            "{}",
            match self {
                FilterType::Text => "text".to_string(),
                FilterType::Date => "date".to_string(),
                FilterType::Number => "number".to_string(),
                FilterType::Association => "association".to_string(),
            }
        )
    }
}

impl FromSql for FilterType {
    fn column_result(value: ValueRef<'_>) -> FromSqlResult<Self> {
        let value_as_str = value
            .as_str()
            .expect("Invalid Non-string value for FilterType fetched from database");
        match value_as_str {
            "text" => FromSqlResult::Ok(FilterType::Text),
            "date" => FromSqlResult::Ok(FilterType::Date),
            "number" => FromSqlResult::Ok(FilterType::Number),
            "association" => FromSqlResult::Ok(FilterType::Association),
            _ => FromSqlResult::Err(FromSqlError::InvalidType),
        }
    }
}

impl ToSql for FilterType {
    fn to_sql(&self) -> rusqlite::Result<ToSqlOutput<'_>> {
        rusqlite::Result::Ok(ToSqlOutput::from(self.to_string()))
    }
}
