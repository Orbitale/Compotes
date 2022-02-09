use crate::entities::tag_rules;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn tag_rules_get(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    serde_json::to_string(&tag_rules::find_all(&conn))
        .expect("Could not serialize Tag rules properly")
}
