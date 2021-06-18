use rusqlite::Connection;
use rusqlite::OpenFlags;
use crate::config::compotes_dir;
use std::path::PathBuf;

pub(crate) fn get_database_connection() -> Connection {
    let database_path = get_database_path();
    let database_flags = get_database_flags();

    Connection::open_with_flags(database_path, database_flags)
        .expect("Could not open database.")
}

fn get_database_path() -> PathBuf {
    compotes_dir().join("compotes.db3")
}

fn get_database_flags() -> OpenFlags {
    let mut db_flags = OpenFlags::empty();

    db_flags.insert(OpenFlags::SQLITE_OPEN_READ_WRITE);
    db_flags.insert(OpenFlags::SQLITE_OPEN_CREATE);
    db_flags.insert(OpenFlags::SQLITE_OPEN_FULL_MUTEX);
    db_flags.insert(OpenFlags::SQLITE_OPEN_NOFOLLOW);
    db_flags.insert(OpenFlags::SQLITE_OPEN_PRIVATE_CACHE);

    db_flags
}
