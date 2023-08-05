// Prevents additional console window on Windows in release, DO NOT REMOVE!!
#![cfg_attr(not(debug_assertions), windows_subsystem = "windows")]

use crate::db::get_database_connection;
use std::sync::Mutex;

mod config;
mod db;
mod migrations;

mod commands {
    pub(crate) mod bank_account_create;
    pub(crate) mod bank_account_update;
    pub(crate) mod bank_account_find_all;
    pub(crate) mod import_operations;
    pub(crate) mod operation_delete;
    pub(crate) mod operation_update_details;
    pub(crate) mod operation_update_tags;
    pub(crate) mod operation_update_ignore_from_analytics;
    pub(crate) mod operations_get;
    pub(crate) mod operations_get_analytics;
    pub(crate) mod operations_get_by_id;
    pub(crate) mod operations_get_count;
    pub(crate) mod operations_get_triage;
    pub(crate) mod operations_get_triage_count;
    pub(crate) mod sync;
    pub(crate) mod tag_create;
    pub(crate) mod tag_rule_create;
    pub(crate) mod tag_rule_update;
    pub(crate) mod tag_rules_get;
    pub(crate) mod tag_update;
    pub(crate) mod tags_get;
}

mod entities {
    pub(crate) mod bank_accounts;
    pub(crate) mod operations;
    pub(crate) mod tag_rules;
    pub(crate) mod tags;
}

mod serialization {
    pub(crate) mod deserialize_tags_ids;
}

mod structs {
    pub(crate) mod filter;
    pub(crate) mod filter_type;
    pub(crate) mod operation_state;
}

fn main() {
    let mut conn = get_database_connection();

    let migrations = rusqlite_migration::Migrations::new(migrations::migrations());
    migrations.to_latest(&mut conn).unwrap();

    tauri::Builder::default()
        .manage(Mutex::new(conn))
        .invoke_handler(tauri::generate_handler![
            crate::commands::operation_delete::operation_delete,
            crate::commands::operation_update_details::operation_update_details,
            crate::commands::operation_update_tags::operation_update_tags,
            crate::commands::operation_update_ignore_from_analytics::operation_update_ignore_from_analytics,
            crate::commands::operations_get_by_id::operations_get_by_id,
            crate::commands::operations_get::operations_get,
            crate::commands::operations_get_analytics::operations_get_analytics,
            crate::commands::operations_get_count::operations_get_count,
            crate::commands::operations_get_triage::operations_get_triage,
            crate::commands::operations_get_triage_count::operations_get_triage_count,
            crate::commands::bank_account_find_all::bank_account_find_all,
            crate::commands::bank_account_create::bank_account_create,
            crate::commands::bank_account_update::bank_account_update,
            crate::commands::tags_get::tags_get,
            crate::commands::tag_rules_get::tag_rules_get,
            crate::commands::tag_create::tag_create,
            crate::commands::tag_update::tag_update,
            crate::commands::tag_rule_create::tag_rule_create,
            crate::commands::tag_rule_update::tag_rule_update,
            crate::commands::import_operations::import_operations,
            crate::commands::sync::sync,
        ])
        .run(tauri::generate_context!())
        .expect("Error while running tauri application");
}
