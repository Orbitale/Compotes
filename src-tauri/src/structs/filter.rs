use serde::Deserialize;
use crate::structs::filter_type::FilterType;

#[derive(Debug, Deserialize)]
#[serde(try_from = "DeserializedFilter")]
pub(crate) struct Filter {
    pub(crate) name: String,
    pub(crate) value: String,
    #[serde(rename = "type")]
    pub(crate) filter_type: FilterType,
}

impl Filter {
    pub(crate) fn to_sql_statement(&self) -> String {
        match self.filter_type {
            FilterType::Text => format!("{field} LIKE ?", field=self.name.clone()),
            FilterType::Date => format!("{field} >= ? AND {field} <= ?", field=self.name.clone()),
            FilterType::Number => format!("{field} >= ? AND {field} <= ?", field=self.name.clone()),
            FilterType::Tags => String::from("
                (
                    SELECT GROUP_CONCAT(tags.name, \",\")
                    FROM operation_tag
                    LEFT JOIN tags ON tags.id = operation_tag.tag_id
                    WHERE operation_tag.operation_id = operations.id
                ) LIKE ?
            "),
        }
    }
}

impl Clone for Filter {
    fn clone(&self) -> Self {
        Filter {
            name: self.name.clone(),
            value: self.value.clone(),
            filter_type: self.filter_type.clone()
        }
    }
}

#[derive(Deserialize)]
struct DeserializedFilter {
    pub(crate) name: String,
    pub(crate) value: String,
    #[serde(rename = "type")]
    pub(crate) filter_type: FilterType,
}


pub struct FilterValidationError;

// The error type has to implement Display
impl std::fmt::Display for FilterValidationError {
    fn fmt(&self, formatter: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
        write!(formatter, "option1 and option2 cannot be null")
    }
}

impl std::convert::TryFrom<DeserializedFilter> for Filter {
    type Error = FilterValidationError;
    fn try_from(shadow: DeserializedFilter) -> Result<Self, Self::Error> {
        let DeserializedFilter { name, value, filter_type } = shadow;

        let value = match filter_type {
            FilterType::Text |
            FilterType::Tags => {
                format!("%{}%", value)
            },
            FilterType::Date
            | FilterType::Number => {
                format!("{}", value)
            },
        };

        Ok(Filter { name, value, filter_type })
    }
}
