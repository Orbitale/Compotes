use crate::entities::operations;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn operations_get(
    conn_state: State<'_, Mutex<Connection>>,
    page: u16,
    order_field: Option<String>,
    order_by: Option<String>,
) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    serde_json::to_string(&operations::find_paginate(conn, page, order_field, order_by))
        .expect("Could not serialize Operations properly")
}
