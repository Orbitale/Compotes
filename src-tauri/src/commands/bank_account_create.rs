use crate::entities::bank_accounts;
use crate::entities::bank_accounts::BankAccount;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn bank_account_create(conn_state: State<'_, Mutex<Connection>>, bank_account: String) -> i64
{
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    let bank_account_entity: BankAccount = serde_json::from_str(&bank_account).unwrap();

    bank_accounts::create(conn, bank_account_entity)
}
