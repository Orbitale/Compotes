
use rusqlite::Connection;
use rusqlite::NO_PARAMS;
use serde::Serialize;
use serde::Deserialize;
use serde_rusqlite::from_rows;

#[derive(Serialize, Deserialize)]
pub(crate) struct Tag
{
    pub(crate) id: u32,
    pub(crate) name: String,
}

pub(crate) fn find_all(conn: &Connection) -> Vec<Tag>
{
    let mut stmt = conn.prepare("
        SELECT
            id,
            name
        FROM tags
        ORDER BY name ASC
    ").expect("Could not fetch tags");

    let mut tags: Vec<Tag> = Vec::new();

    let mut rows_iter = from_rows::<Tag>(stmt.query(NO_PARAMS).unwrap());

    loop {
        match rows_iter.next() {
            None => { break; },
            Some(tag) => {
                let tag = tag.expect("Could not deserialize Tag item");
                tags.push(tag);
            }
        }
    }

    tags
}
