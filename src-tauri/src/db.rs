use crate::config::compotes_dir;
use rusqlite::Connection;
use rusqlite::OpenFlags;
use rusqlite::functions::Context;
use rusqlite::functions::FunctionFlags;
use std::path::PathBuf;
use regex::Regex;

pub(crate) fn get_database_connection() -> Connection {
    let database_path = get_database_path();
    let database_flags = get_database_flags();

    let conn = Connection::open_with_flags(database_path, database_flags).expect("Could not open database.");

    conn.create_scalar_function(
        "regexp",
        2,
        FunctionFlags::SQLITE_UTF8 | FunctionFlags::SQLITE_DETERMINISTIC,
        regexp_with_auxilliary,
    ).expect("Could not add regexp function to database");

    conn
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


// Code borrowed from rusqlite's unit test suite that implemented regex with auxilliary functions.
// --------------------------------------
// This implementation of a regexp scalar function uses SQLite's auxiliary data
// (https://www.sqlite.org/c3ref/get_auxdata.html) to avoid recompiling the regular
// expression multiple times within one query.
fn regexp_with_auxilliary(ctx: &Context<'_>) -> rusqlite::Result<bool> {
    assert_eq!(ctx.len(), 2, "called with unexpected number of arguments");
    type BoxError = Box<dyn std::error::Error + Send + Sync + 'static>;
    let regexp: std::sync::Arc<Regex> = ctx
        .get_or_create_aux(0, |vr| -> Result<_, BoxError> {
            Ok(Regex::new(vr.as_str()?)?)
        })?;

    let is_match = {
        let text = ctx
            .get_raw(1)
            .as_str()
            .map_err(|e| rusqlite::Error::UserFunctionError(e.into()))?;

        regexp.is_match(text)
    };

    Ok(is_match)
}