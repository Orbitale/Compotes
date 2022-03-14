use crate::entities::operations;
use crate::entities::operations::Operation;
use rusqlite::Connection;
use std::ops::DerefMut;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn import_operations(
    _conn_state: State<'_, Mutex<Connection>>,
    operations: Vec<Operation>,
) {
    let mut conn = _conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref_mut();

    operations::insert_all(conn, operations);
}
