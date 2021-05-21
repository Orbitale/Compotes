#![cfg_attr(
all(not(debug_assertions), target_os = "windows"),
windows_subsystem = "windows"
)]

use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;
use crate::entities::operations;
use crate::entities::bank_accounts;
use crate::entities::tags;
use crate::entities::tag_rules;
use crate::entities::tags::Tag;

mod entities {
    pub(crate) mod operations;
    pub(crate) mod bank_accounts;
    pub(crate) mod tags;
    pub(crate) mod tag_rules;
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
            get_operations,
            get_bank_accounts,
            get_tags,
            get_tag_rules,
            save_tag,
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

    serde_json::to_string(&operations::find_all(&conn)).expect("Could not serialize Operations properly")
}

#[tauri::command]
fn get_bank_accounts(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state.inner().lock().expect("Could not retrieve connection");
    let conn = conn.deref();

    serde_json::to_string(&bank_accounts::find_all(&conn)).expect("Could not serialize BankAccount properly")
}

#[tauri::command]
fn get_tags(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state.inner().lock().expect("Could not retrieve connection");
    let conn = conn.deref();

    serde_json::to_string(&tags::find_all(&conn)).expect("Could not serialize Tag properly")
}

#[tauri::command]
fn get_tag_rules(conn_state: State<'_, Mutex<Connection>>) -> String {
    let conn = conn_state.inner().lock().expect("Could not retrieve connection");
    let conn = conn.deref();

    serde_json::to_string(&tag_rules::find_all(&conn)).expect("Could not serialize Tag rules properly")
}

#[tauri::command]
fn save_tag(conn_state: State<'_, Mutex<Connection>>, tag: String) {
    let conn = conn_state.inner().lock().expect("Could not retrieve connection");
    let conn = conn.deref();

    let tag_entity: Tag = serde_json::from_str(&tag).unwrap();
    tags::save(conn, tag_entity);
}
