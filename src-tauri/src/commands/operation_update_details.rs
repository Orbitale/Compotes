use crate::entities::operations;
use rusqlite::Connection;
use std::ops::DerefMut;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn operation_update_details(
    _conn_state: State<'_, Mutex<Connection>>,
    id: String,
    details: String,
) {
    let mut conn = _conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref_mut();

    operations::update_details(conn, id, details);
    println!("Todo: update other operations with same hash.");
}
