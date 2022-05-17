use crate::entities::operations;
use crate::structs::filter::Filter;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;
use crate::entities::operations::Operation;

#[tauri::command]
pub(crate) fn operations_get_analytics(
    conn_state: State<'_, Mutex<Connection>>,
    filters: Option<Vec<Filter>>,
) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    let result: Vec<Operation> = operations::find_analytics(conn, filters).expect("Could not fetch operations for analytics");

    serde_json::to_string(&result).expect("Could not serialize operations for analytics")
}
