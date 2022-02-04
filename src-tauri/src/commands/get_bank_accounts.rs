use crate::entities::bank_accounts;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn get_bank_accounts(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    serde_json::to_string(&bank_accounts::find_all(&conn))
        .expect("Could not serialize BankAccount properly")
}
