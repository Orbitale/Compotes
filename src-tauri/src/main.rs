#![cfg_attr(
all(not(debug_assertions), target_os = "windows"),
windows_subsystem = "windows"
)]

use std::sync::Mutex;
use crate::db::get_database_connection;

mod db;
mod config;

mod commands {
    pub(crate) mod get_operations;
    pub(crate) mod get_bank_accounts;
    pub(crate) mod save_bank_account;
    pub(crate) mod get_tags;
    pub(crate) mod get_tag_rules;
    pub(crate) mod save_tag;
    pub(crate) mod save_tag_rule;
    pub(crate) mod import_operations;
    pub(crate) mod sync;
}

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
    let mut conn = get_database_connection();

    embedded::migrations::runner().run(&mut conn).expect("Could not execute database migrations.");

    tauri::Builder::default()
        .manage(Mutex::new(conn))
        .invoke_handler(tauri::generate_handler![
            crate::commands::get_operations::get_operations,
            crate::commands::get_bank_accounts::get_bank_accounts,
            crate::commands::save_bank_account::save_bank_account,
            crate::commands::get_tags::get_tags,
            crate::commands::get_tag_rules::get_tag_rules,
            crate::commands::save_tag::save_tag,
            crate::commands::save_tag_rule::save_tag_rule,
            crate::commands::import_operations::import_operations,
            crate::commands::sync::sync,
        ])
        .run(tauri::generate_context!())
        .expect("Error while running tauri application");
}

mod embedded {
    refinery::embed_migrations!("src/migrations/");
}
