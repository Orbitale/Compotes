#![cfg_attr(
all(not(debug_assertions), target_os = "windows"),
windows_subsystem = "windows"
)]

use rusqlite::Connection;
use rusqlite::NO_PARAMS;
use std::ops::Deref;
use std::sync::Mutex;
use serde_rusqlite::from_rows;
use tauri::State;
use crate::entities::operation::Operation;

mod entities {
    pub(crate) mod operation;
}

mod structs {
    pub(crate) mod operation_state;
}

fn main() {
    let mut conn = Connection::open("./data.db3").expect("Could not open database.");

    embedded::migrations::runner().run(&mut conn).expect("Could not execute database migrations.");

    tauri::Builder::default()
        .manage(Mutex::new(conn))
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
fn get_operations(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state.inner().lock().expect("Could not retrieve connection");
    let conn = conn.deref();

    let mut stmt = conn.prepare("
        SELECT
            id,
            operation_date,
            type as op_type,
            type_display,
            details,
            amount_in_cents,
            hash,
            state,
            bank_account_id,
            ignored_from_charts
        FROM operations
    ").expect("Could not fetch operations");

    let mut operations: Vec<Operation> = Vec::new();

    let mut rows_iter = from_rows::<Operation>(stmt.query(NO_PARAMS).unwrap());

    loop {
        match rows_iter.next() {
            None => { break; },
            Some(operation) => {
                let operation = operation.expect("Could not deserialize Operation item");
                operations.push(operation);
            }
        }
    }

    serde_json::to_string(&operations).expect("Could not serialize Operations properly")
}
