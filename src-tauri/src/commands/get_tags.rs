use std::ops::Deref;
use rusqlite::Connection;
use std::sync::Mutex;
use tauri::State;
use crate::entities::tags;

#[tauri::command]
pub(crate) fn get_tags(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state.inner().lock().expect("Could not retrieve database connection");
    let conn = conn.deref();

    serde_json::to_string(&tags::find_all(&conn)).expect("Could not serialize Tag properly")
}
