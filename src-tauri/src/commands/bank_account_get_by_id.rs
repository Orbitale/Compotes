use crate::entities::bank_accounts;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn bank_account_get_by_id(conn_state: State<'_, Mutex<Connection>>, id: String) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    let id_as_u32: u32 = id.parse().unwrap();

    serde_json::to_string(&bank_accounts::get_by_id(conn, id_as_u32))
        .expect("Could not serialize BankAccount properly")
}
