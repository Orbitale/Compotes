use tauri::api::path::home_dir;
use std::path::PathBuf;
use std::fs;

pub(crate) fn compotes_dir() -> PathBuf {
    let compotes_dir = home_dir()
        .expect("Could not determine HOME_DIR to store database.")
        .join(".compotes")
    ;

    if !compotes_dir.exists() {
        fs::create_dir_all(&compotes_dir).expect("Could not create Compotes directory.");
    }

    return compotes_dir;
}
