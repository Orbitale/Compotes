use crate::entities::operations;
use crate::structs::filter::Filter;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn operations_get_count(
    conn_state: State<'_, Mutex<Connection>>,
    filters: Option<Vec<Filter>>,
) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    serde_json::to_string(&operations::find_count(conn, filters).expect("Could not fetch operations count"))
        .expect("Could not serialize Operations properly")
}
