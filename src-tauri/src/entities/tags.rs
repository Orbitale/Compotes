use rusqlite::named_params;
use rusqlite::Connection;
use serde::Deserialize;
use serde::Serialize;
use serde_rusqlite::from_rows;

#[derive(Serialize, Deserialize, Debug)]
pub(crate) struct Tag {
    pub(crate) id: u32,
    pub(crate) name: String,
}

pub(crate) fn find_all(conn: &Connection) -> Vec<Tag> {
    let mut stmt = conn
        .prepare(
            "
        SELECT
            id,
            name
        FROM tags
        ORDER BY name ASC
    ",
        )
        .unwrap();

    let tags_result = stmt.query([]).expect("Could not fetch tags");

    let mut tags: Vec<Tag> = Vec::new();

    let mut rows_iter = from_rows::<Tag>(tags_result);

    loop {
        match rows_iter.next() {
            None => {
                break;
            }
            Some(tag) => {
                let tag = tag.expect("Could not deserialize Tag item");
                tags.push(tag);
            }
        }
    }

    tags
}

pub(crate) fn update(conn: &Connection, tag: Tag) {
    let mut stmt = conn
        .prepare(
            "
        UPDATE tags
        SET name = :name
        WHERE id = :id
    ",
        )
        .unwrap();

    stmt.execute(named_params! {
        ":id": &tag.id,
        ":name": &tag.name,
    })
    .expect("Could not update tag");
}

pub(crate) fn create(conn: &Connection, tag: Tag) -> i64 {
    let mut stmt = conn
        .prepare(
            "
        INSERT INTO tags (id, name)
        VALUES (null, :name)
    ",
        )
        .unwrap();

    stmt.execute(named_params! {
        ":name": &tag.name,
    })
    .expect("Could not update tag");

    conn.last_insert_rowid()
}
