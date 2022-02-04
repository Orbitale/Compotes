use rusqlite::Connection;
use std::sync::Mutex;
use tauri::State;
use crate::entities::operations;
use crate::entities::tag_rules;

#[tauri::command]
pub(crate) fn sync(
    _conn_state: State<'_, Mutex<Connection>>
) -> String {
    operations::refresh_statuses_with_hashes();
    tag_rules::apply_rules();
    std::thread::sleep(std::time::Duration::from_secs(1));

    return "1".to_string();
}
