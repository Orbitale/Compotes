#![cfg_attr(
all(not(debug_assertions), target_os = "windows"),
windows_subsystem = "windows"
)]

use rusqlite::Connection;

fn main() {
    let mut db_connection = Connection::open("./data.db3").expect("Could not open database.");

    embedded::migrations::runner().run(&mut db_connection).expect("Could not execute database migrations.");

    tauri::Builder::default()
        .invoke_handler(tauri::generate_handler![
            my_custom_command
        ])
        .run(tauri::generate_context!())
        .expect("error while running tauri application");
}

#[tauri::command]
fn my_custom_command() -> String {
    "I was invoked from JS!".into()
}

mod embedded {
    refinery::embed_migrations!("src/migrations/");
}
