use crate::entities::operations;
use rusqlite::Connection;
use std::ops::DerefMut;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn operation_delete(
    _conn_state: State<'_, Mutex<Connection>>,
    id: String,
) {
    let mut conn = _conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let mut conn = conn.deref_mut();

    operations::delete(&mut conn, id);
    println!("Todo: update other operations with same hash.");
}