
use rusqlite::Connection;
use rusqlite::NO_PARAMS;
use serde::Serialize;
use serde::Deserialize;
use serde_rusqlite::from_rows;

#[derive(Serialize, Deserialize, Debug, Default)]
pub(crate) struct TagRule
{
    pub(crate) id: u32,
    #[serde(deserialize_with = "deserialize_tags_ids")]
    pub(crate) tags_ids: Vec<u32>,
    pub(crate) matching_pattern: String,
    pub(crate) is_regex: bool
}

pub fn deserialize_tags_ids<'de, D>(deserializer: D) -> Result<Vec<u32>, D::Error>
    where
        D: serde::de::Deserializer<'de>,
{
    struct StringVecVisitor;

    impl<'de> serde::de::Visitor<'de> for StringVecVisitor {
        type Value = Vec<u32>;

        fn expecting(&self, formatter: &mut std::fmt::Formatter) -> std::fmt::Result {
            formatter.write_str("a string containing a list of integer IDs separated by commas.")
        }

        fn visit_str<E>(self, v: &str) -> Result<Self::Value, E>
            where
                E: serde::de::Error,
        {
            let mut ids = Vec::new();
            for id in v.split(",") {
                let id = id.parse::<u32>().unwrap_or(0);
                if id != 0 {
                    ids.push(id);
                }
            }
            Ok(ids)
        }
    }

    deserializer.deserialize_any(StringVecVisitor)
}

pub(crate) fn find_all(conn: &Connection) -> Vec<TagRule>
{
    let mut stmt = conn.prepare("
        SELECT id,
        matching_pattern,
        is_regex,
        (
            SELECT GROUP_CONCAT(tag_id)
            FROM tag_rule_tag
            WHERE tag_rule_id = tag_rules.id
        ) AS tags_ids
        FROM tag_rules
    ").expect("Could not fetch tag_rules");

    let mut tag_rules: Vec<TagRule> = Vec::new();

    let mut rows_iter = from_rows::<TagRule>(stmt.query(NO_PARAMS).unwrap());

    loop {
        match rows_iter.next() {
            None => { break; },
            Some(tag_rule) => {
                let tag_rule = tag_rule.expect("Could not deserialize TagRule item");
                tag_rules.push(tag_rule);
            }
        }
    }

    tag_rules
}

pub(crate) fn save(conn: &Connection, tag_rule: TagRule)
{
    let mut stmt = conn.prepare("
        UPDATE tag_rules
        SET
            is_regex = :is_regex,
            matching_pattern = :matching_pattern
        WHERE id = :id
    ").unwrap();

    stmt.execute_named(&[
        (":id", &tag_rule.id),
        (":is_regex", &tag_rule.is_regex),
        (":matching_pattern", &tag_rule.matching_pattern),
    ]).expect("Could not update tag");
}
