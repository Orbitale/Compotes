#![cfg_attr(
all(not(debug_assertions), target_os = "windows"),
windows_subsystem = "windows"
)]

use chrono::prelude::*;
use rusqlite::{Connection, ToSql};
use tauri::State;
use std::sync::Mutex;
use std::ops::Deref;
use rusqlite::types::{FromSql, ToSqlOutput};
use rusqlite::types::FromSqlResult;
use rusqlite::types::ValueRef;
use rusqlite::types::FromSqlError;
use chrono::ParseResult;

fn main() {
    let mut conn = Connection::open("./data.db3").expect("Could not open database.");

    embedded::migrations::runner().run(&mut conn).expect("Could not execute database migrations.");

    tauri::Builder::default()
        .manage(Mutex::new(conn))
        .invoke_handler(tauri::generate_handler![
            get_operations
        ])
        .run(tauri::generate_context!())
        .expect("error while running tauri application");
}

mod embedded {
    refinery::embed_migrations!("src/migrations/");
}

struct Database(Mutex<Connection>);

#[tauri::command]
fn get_operations(conn_state: State<'_, Database>) -> String {
    let conn = conn_state.0.lock().unwrap();
    let conn: &Connection = conn.deref();

    let mut stmt = conn.prepare("
        SELECT
            id,
            operation_date,
            type,
            type_display,
            details,
            amount_in_cents,
            hash,
            state,
            bank_account_id,
            ignored_from_charts
        FROM operations
    ").expect("Could not fetch operations");

    let operations = stmt.query_map(&arr, |row| {
        Ok(Operation::<Utc> {
            _id: row.get(0).expect("Could not hydrate field \"id\""),
            _operation_date: row.get(1).expect("Could not hydrate field \"operation_date\""),
            _type: row.get(2).expect("Could not hydrate field \"type\""),
            _type_display: row.get(3).expect("Could not hydrate field \"type_display\""),
            _details: row.get(4).expect("Could not hydrate field \"details\""),
            _amount_in_cents: row.get(5).expect("Could not hydrate field \"amount_in_cents\""),
            _hash: row.get(6).expect("Could not hydrate field \"hash\""),
            _state: row.get(7).expect("Could not hydrate field \"state\""),
            _bank_account_id: row.get(8).expect("Could not hydrate field \"bank_account_id\""),
            _ignored_from_charts: row.get(9).expect("Could not hydrate field \"ignored_from_charts\""),
        })
    }).expect("Could not create operations iterator");

    "{}".into()
}

struct DbDateTime<TimeZone>
where TimeZone: chrono::TimeZone
{
    _date: DateTime<TimeZone>
}

impl FromSql for DbDateTime<Utc> {
    fn column_result(value: ValueRef<'_>) -> FromSqlResult<Self> {
        let value_as_str = value.as_str().expect("Invalid Non-string value for datetime fetched from database");
        let datetime = DateTime::parse_from_str(value_as_str, "");

        match datetime {
            ParseResult::Ok(datetime) => {
                FromSqlResult::Ok(DbDateTime{ _date: DateTime::<Utc>::from(datetime) })
            },
            ParseResult::Err(e) => FromSqlResult::Err(FromSqlError::Other(e.into()))
        }
    }
}

impl FromSql for DbDateTime<FixedOffset> {
    fn column_result(value: ValueRef<'_>) -> FromSqlResult<Self> {
        let value_as_str = value.as_str().expect("Invalid Non-string value for datetime fetched from database");
        let datetime = DateTime::parse_from_str(value_as_str, "");

        match datetime {
            ParseResult::Ok(datetime) => FromSqlResult::Ok(DbDateTime { _date: datetime }),
            ParseResult::Err(e) => FromSqlResult::Err(FromSqlError::Other(e.into()))
        }
    }
}

enum OperationState {
    Ok,
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

struct Operation<TimeZone>
where TimeZone: chrono::TimeZone
{
    _id: u32,
    _operation_date: DbDateTime<TimeZone>,
    _type: String,
    _type_display: String,
    _details: String,
    _amount_in_cents: u32,
    _hash: String,
    _state: OperationState,
    _ignored_from_charts: bool,
    _bank_account_id: u32,
}

impl ToSql for Operation<Utc>
{
    fn to_sql(&self) -> Result<ToSqlOutput<'_>, rusqlite::Error> {
        todo!()
    }
}
