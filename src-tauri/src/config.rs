use std::fs;
use std::path::PathBuf;
use tauri::api::path::home_dir;

pub(crate) const NUMBER_PER_PAGE: u16 = 20;

pub(crate) fn compotes_dir() -> PathBuf {
    let compotes_dir = home_dir()
        .expect("Could not determine HOME_DIR to store database.")
        .join(".compotes");

    if !compotes_dir.exists() {
        fs::create_dir_all(&compotes_dir).expect("Could not create Compotes directory.");
    }

    compotes_dir
}
