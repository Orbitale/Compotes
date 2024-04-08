use rusqlite::named_params;
use rusqlite::Connection;
use serde::Deserialize;
use serde::Serialize;

#[derive(Serialize, Deserialize, Debug, Default)]
pub(crate) struct TagRule {
    pub(crate) id: u32,
    #[serde(deserialize_with = "crate::serialization::deserialize_tags_ids::deserialize_tags_ids")]
    pub(crate) tags_ids: Vec<u32>,
    pub(crate) matching_pattern: String,
    pub(crate) is_regex: bool,
}

#[derive(Deserialize)]
struct TagRuleToApply {
    tag_id: u32,
    matching_pattern: String,
    is_regex: bool,
}

pub(crate) fn find_all(conn: &Connection) -> Vec<TagRule> {
    let mut stmt = conn
        .prepare(
            "
        SELECT id,
        matching_pattern,
        is_regex,
        (
            SELECT GROUP_CONCAT(tag_id)
            FROM tag_rule_tag
            WHERE tag_rule_id = tag_rules.id
        ) AS tags_ids
        FROM tag_rules
        ORDER BY length(matching_pattern) DESC
    ",
        )
        .expect("Could not fetch tag_rules");

    let mut tag_rules: Vec<TagRule> = Vec::new();

    let mut rows_iter = serde_rusqlite::from_rows::<TagRule>(stmt.query([]).unwrap());

    loop {
        match rows_iter.next() {
            None => {
                break;
            }
            Some(tag_rule) => {
                let tag_rule = tag_rule.expect("Could not deserialize TagRule item");
                tag_rules.push(tag_rule);
            }
        }
    }

    tag_rules
}

pub(crate) fn update(conn: &Connection, tag_rule: TagRule) {
    // Update tag rule
    let mut stmt = conn
        .prepare(
            "
        UPDATE tag_rules
        SET is_regex = :is_regex,
            matching_pattern = :matching_pattern
        WHERE id = :id
    ",
        )
        .unwrap();

    stmt.execute(named_params! {
        ":id": &tag_rule.id,
        ":is_regex": &tag_rule.is_regex,
        ":matching_pattern": &tag_rule.matching_pattern,
    })
    .expect("Could not update tag");

    // Remove previous tags associations
    let mut stmt = conn
        .prepare("DELETE FROM tag_rule_tag WHERE tag_rule_id = :id")
        .expect("Could not create query to delete tag rule associations");

    stmt.execute(named_params! {":id": &tag_rule.id})
        .expect("Could not delete tag rule associations");

    let mut stmt = conn
        .prepare(
            "
        INSERT INTO tag_rule_tag (
            tag_rule_id,
            tag_id
        )
        VALUES (
            :tag_rule_id,
            :tag_id
        )
        ",
        )
        .expect("Could not create query to insert tag rule with tag association");

    for tags_id in tag_rule.tags_ids {
        stmt.execute(named_params! {
            ":tag_rule_id": &tag_rule.id,
            ":tag_id": &tags_id,
        })
        .expect("Could not insert tag rule with tag associations");
    }
}

pub(crate) fn apply_rules(conn: &mut Connection) -> (u32, u32) {
    let mut rules_to_apply: Vec<TagRuleToApply> = Vec::new();

    {
        let mut stmt = conn
            .prepare(
                "
            SELECT
               tags.id as tag_id,
               tag_rules.matching_pattern,
               tag_rules.is_regex
            FROM tag_rule_tag
            INNER JOIN tags ON tag_rule_tag.tag_id = tags.id
            INNER JOIN tag_rules ON tag_rule_tag.tag_rule_id = tag_rules.id
        ",
            )
            .expect("Could not fetch operations");

        let mut rows = serde_rusqlite::from_rows::<TagRuleToApply>(stmt.query([]).unwrap());

        loop {
            match rows.next() {
                None => {
                    break;
                }
                Some(result_rule_to_apply) => {
                    let rule_to_apply =
                        result_rule_to_apply.expect("Could not deserialize TagRule item");
                    rules_to_apply.push(rule_to_apply);
                }
            }
        }
    }

    let number_of_rules: u32 = rules_to_apply.len() as u32;

    let mut number_of_affected_operations: u32 = 0;

    for rule in rules_to_apply {
        let sql = format!(
            "
        INSERT OR IGNORE INTO operation_tag (operation_id, tag_id)
        SELECT id as operation_id, :tag_id
        FROM operations
        WHERE {}
        ",
            if rule.is_regex {
                "regexp(:pattern, details)"
            } else {
                "details LIKE :pattern"
            }
        );

        let pattern = if rule.is_regex {
            rule.matching_pattern
        } else {
            format!("%{}%", rule.matching_pattern)
        };

        // Stmt to exec if operation matches
        let mut stmt = conn
            .prepare_cached(&sql)
            .expect("Could not create query to apply a tag rule.");

        let affected_rows =
            stmt.execute(named_params! {
                ":pattern": &pattern,
                ":tag_id": rule.tag_id,
            })
            .expect("Could not execute insert statement for operation tag.") as u32;

        number_of_affected_operations += affected_rows;
    }

    (number_of_rules, number_of_affected_operations)
}

pub(crate) fn create(conn: &Connection, tag_rule: TagRule) -> usize {
    let mut stmt = conn
        .prepare(
            "
            INSERT INTO tag_rules (
                is_regex,
                matching_pattern
            )
            VALUES (
                :is_regex,
                :matching_pattern
            )
        ",
        )
        .expect("Could not create query to create new tag rule");

    stmt.execute(named_params! {
        ":is_regex": &tag_rule.is_regex,
        ":matching_pattern": &tag_rule.matching_pattern,
    })
    .expect("Could not insert tag");

    let id = conn.last_insert_rowid();

    let mut stmt = conn
        .prepare(
            "
        INSERT INTO tag_rule_tag (
            tag_rule_id,
            tag_id
        )
        VALUES (
            :tag_rule_id,
            :tag_id
        )
        ",
        )
        .expect("Could not create query to insert tag rule with tag association");

    for tags_id in tag_rule.tags_ids {
        stmt.execute(named_params! {
            ":tag_rule_id": &id,
            ":tag_id": &tags_id,
        })
        .expect("Could not insert tag rule with tag association");
    }

    id as usize
}
