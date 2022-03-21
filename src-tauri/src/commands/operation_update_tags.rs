use crate::entities::operations;
use rusqlite::Connection;
use std::ops::DerefMut;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn operation_update_tags(
    _conn_state: State<'_, Mutex<Connection>>,
    id: String,
    tags: Vec<u32>,
) {
    let mut conn = _conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref_mut();

    operations::update_tags(conn, id, tags);
}