#![cfg_attr(
all(not(debug_assertions), target_os = "windows"),
windows_subsystem = "windows"
)]

use rusqlite::Connection;
use chrono::prelude::*;

fn main() {
    let mut db_connection = Connection::open("./data.db3").expect("Could not open database.");

    embedded::migrations::runner().run(&mut db_connection).expect("Could not execute database migrations.");

    tauri::Builder::default()
        .invoke_handler(tauri::generate_handler![
            get_operations
        ])
        .run(tauri::generate_context!())
        .expect("error while running tauri application");
}

mod embedded {
    refinery::embed_migrations!("src/migrations/");
}

#[tauri::command]
fn get_operations() -> String {
    "{}".into()
}

enum OperationState {
    Ok,
    PendingTriage,
}

struct Operation {
    _id: usize,
    _operation_date: String,
    _type: DateTime<Utc>,
    _type_display: String,
    _details: String,
    _amount_in_cents: isize,
    _hash: String,
    _state: OperationState,
    _ignored_from_charts: bool,
    _bank_account_id: usize,
}
