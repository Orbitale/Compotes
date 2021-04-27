#![cfg_attr(
  all(not(debug_assertions), target_os = "windows"),
  windows_subsystem = "windows"
)]

use tauri_plugin_stronghold::TauriStronghold;

fn main() {
  tauri::Builder::default()
    .invoke_handler(tauri::generate_handler![
        my_custom_command
    ])
    .plugin(TauriStronghold::default())
    .run(tauri::generate_context!())
    .expect("error while running tauri application");
}

#[tauri::command]
fn my_custom_command() -> String {
    "I was invoked from JS!".into()
}
