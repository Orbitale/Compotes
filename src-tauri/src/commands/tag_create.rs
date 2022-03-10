use crate::entities::tags;
use crate::entities::tags::Tag;
use rusqlite::Connection;
use std::ops::Deref;
use std::sync::Mutex;
use tauri::State;

#[tauri::command]
pub(crate) fn tag_create(conn_state: State<'_, Mutex<Connection>>, tag: String) -> i64
{
    let conn = conn_state
        .inner()
        .lock()
        .expect("Could not retrieve database connection");
    let conn = conn.deref();

    let tag_entity: Tag = serde_json::from_str(&tag).unwrap();

    tags::create(conn, tag_entity)
}
