use crate::entities::tags;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn tags_get(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    serde_json::to_string(&tags::find_all(conn)).expect("Could not serialize Tag properly")
}
