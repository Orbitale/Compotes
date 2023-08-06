use crate::entities::operations;
use crate::entities::operations::Operation;
use crate::structs::filter::Filter;
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
    filters: Option<Vec<Filter>>,
) -> String {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    let result: Vec<Operation> =
        operations::find_paginate(conn, page, order_field, order_by, filters)
            .expect("Could not fetch operations");

    serde_json::to_string(&result).expect("Could not serialize operations")
}
