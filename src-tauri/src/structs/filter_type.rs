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
    #[serde(rename = "boolean")]
    Boolean,
    #[serde(rename = "entity")]
    Entity,
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
                FilterType::Boolean => "boolean".to_string(),
                FilterType::Entity => "entity".to_string(),
            }
        )
    }
}

impl FromSql for FilterType {
    fn column_result(value: ValueRef<'_>) -> FromSqlResult<Self> {
        let value_as_str = value
            .as_str()?;

        match value_as_str {
            "text" => Ok(FilterType::Text),
            "date" => Ok(FilterType::Date),
            "number" => Ok(FilterType::Number),
            "boolean" => Ok(FilterType::Boolean),
            "entity" => Ok(FilterType::Entity),
            _ => Err(FromSqlError::InvalidType),
        }
    }
}

impl ToSql for FilterType {
    fn to_sql(&self) -> rusqlite::Result<ToSqlOutput<'_>> {
        Ok(ToSqlOutput::from(self.to_string()))
    }
}
