use crate::entities::operations;
use crate::entities::tag_rules;
use rusqlite::Connection;
use serde::Serialize;
use std::ops::DerefMut;
use std::sync::Mutex;
use tauri::State;

#[derive(Serialize)]
struct SyncResult {
    rules_applied: u32,
    affected_operations: u32,
    duplicates_refreshed: u32,
}

impl SyncResult {
    pub fn new(rules_applied: u32, affected_operations: u32, duplicates_refreshed: u32) -> Self {
        SyncResult {
            rules_applied,
            affected_operations,
            duplicates_refreshed,
        }
    }
}

#[tauri::command]
pub(crate) fn sync(conn_state: State<'_, Mutex<Connection>>) -> String {
    let mut conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref_mut();

    let (rules_applied, affected_operations) = tag_rules::apply_rules(conn);
    let duplicates = operations::refresh_statuses_with_hashes(conn);

    serde_json::to_string(&SyncResult::new(
        rules_applied,
        affected_operations,
        duplicates,
    ))
    .unwrap()
}
