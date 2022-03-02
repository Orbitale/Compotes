use crate::entities::operations;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn operations_get_triage_count(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    serde_json::to_string(&operations::find_count_triage(&conn))
        .expect("Could not serialize Operations properly")
}
