use crate::entities::operations;
use crate::entities::tag_rules;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn sync(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    operations::refresh_statuses_with_hashes(&conn);
    tag_rules::apply_rules(&conn);

    return "1".to_string();
}
