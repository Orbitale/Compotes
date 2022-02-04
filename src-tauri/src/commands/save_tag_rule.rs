use crate::entities::tag_rules;
use crate::entities::tag_rules::TagRule;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn save_tag_rule(conn_state: State<'_, Mutex<Connection>>, tag_rule: String) {
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    let tag_rule_entity: TagRule = serde_json::from_str(&tag_rule).unwrap();
    tag_rules::save(conn, tag_rule_entity);
}
