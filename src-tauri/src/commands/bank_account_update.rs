use crate::entities::bank_accounts;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn bank_account_update(
    conn_state: State<'_, Mutex<Connection>>,
    id: u32,
    name: String,
    currency: String,
) {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    bank_accounts::update(conn, id, name, currency);
}
