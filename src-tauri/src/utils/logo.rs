use std::fs::File;
use std::io::Write;
use std::path::PathBuf;
use crate::config::compotes_dir;

pub(crate) fn path() -> PathBuf {
    compotes_dir().join("logo.svg")
}

pub(crate) fn save() {
    let mut icon_file = File::create(path()).unwrap();
    icon_file.write_all(include_bytes!("../../../public/logo.svg")).unwrap();
}

pub(crate) fn exists() -> bool {
    path().is_dir()
}
