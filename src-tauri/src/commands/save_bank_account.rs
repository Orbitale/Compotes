use std::ops::Deref;
use rusqlite::Connection;
use std::sync::Mutex;
use tauri::State;
use crate::entities::bank_accounts::BankAccount;
use crate::entities::bank_accounts;

#[tauri::command]
pub(crate) fn save_bank_account(conn_state: State<'_, Mutex<Connection>>, bank_account: String) {
    let conn = conn_state.inner().lock().expect("Could not retrieve database connection");
    let conn = conn.deref();

    let bank_account_entity: BankAccount = serde_json::from_str(&bank_account).unwrap();
    bank_accounts::save(conn, bank_account_entity);
}
