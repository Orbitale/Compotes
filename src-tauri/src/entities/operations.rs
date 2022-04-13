use crate::structs::operation_state::OperationState;
use crate::entities::bank_accounts;
use crate::structs::filter::Filter;
use crate::structs::filter_type::FilterType;
use rusqlite::named_params;
use rusqlite::ToSql;
use rusqlite::Connection;
use serde::Deserialize;
use serde::Serialize;
use sha2::Digest;
use sha2::Sha512;

#[derive(Debug, Serialize, Deserialize)]
pub(crate) struct Operation {
    pub(crate) id: u32,
    pub(crate) operation_date: String,
    pub(crate) op_type: String,
    pub(crate) type_display: String,
    pub(crate) details: String,
    pub(crate) amount_in_cents: i32,
    pub(crate) hash: String,
    pub(crate) state: OperationState,
    pub(crate) ignored_from_charts: bool,
    pub(crate) bank_account_id: u32,
    #[serde(deserialize_with = "crate::serialization::deserialize_tags_ids::deserialize_tags_ids")]
    pub(crate) tags_ids: Vec<u32>,
}

impl Operation {
    fn compute_hash(self, bank_account_slug: String) -> String {
        compute_hash(
            self.op_type,
            bank_account_slug,
            self.type_display,
            self.details,
            self.operation_date,
            self.amount_in_cents,
        )
    }
}

pub(crate) fn find_paginate(
    conn: &Connection,
    page: u16,
    order_field: Option<String>,
    order_by: Option<String>,
    filters: Option<Vec<Filter>>,
) -> anyhow::Result<Vec<Operation>> {
    let order_field = if order_field.is_some() { order_field.unwrap() } else { "operation_date".to_string() };
    let order_by = if order_by.is_some() { order_by.unwrap() } else { "DESC".to_string() };
    let limit = crate::config::NUMBER_PER_PAGE;
    let offset = ((page - 1) * limit).to_string();

    let sql = "
        SELECT
            id,
            operation_date,
            type as op_type,
            type_display,
            details,
            amount_in_cents,
            hash,
            state,
            bank_account_id,
            ignored_from_charts,
            (
                SELECT GROUP_CONCAT(tag_id)
                FROM operation_tag
                WHERE operation_id = operations.id
            ) AS tags_ids
        FROM operations
        WHERE state != ?
        {{ filters }}
        ORDER BY operations.{{ order_field }} {{ order_by }}
        LIMIT ? OFFSET ?
    ";

    let filters = filters.unwrap_or_default();

    let filters_sql = filters
        .iter()
        .fold(
            "".to_string(),
            |result, filter| {
                let stmt = filter.to_sql_statement();
                format!("{} AND ({})", result, &stmt)
            }
        )
    ;

    let mut sql_params: Vec<&dyn ToSql> = Vec::new();

    let ok = OperationState::PendingTriage.to_string();
    sql_params.push(&ok);

    let sql = sql.replace("{{ order_field }}", &order_field);
    let sql = sql.replace("{{ order_by }}", &order_by);
    let sql = sql.replace("{{ filters }}", &filters_sql);

    let mut sql_values: Vec<String> = Vec::new();

    for filter in filters.iter() {
        match filter.filter_type {
            FilterType::Text => {
                let value = filter.value.to_string();
                sql_values.push(value);
            },
            FilterType::Tags => {
                let value = filter.value.to_string();
                sql_values.push(value);
            },
            FilterType::Number => {
                let min = i64::MIN.to_string();
                let max = i64::MAX.to_string();

                let values = filter.value.split_once(";").unwrap_or((&min, &max));

                let value1 = if values.0 == "".to_string() { i64::MIN } else { 100 * values.0.parse::<i64>()? };
                let value2 = if values.1 == "".to_string() { i64::MAX } else { 100 * values.1.parse::<i64>()? };

                sql_values.push(value1.to_string());
                sql_values.push(value2.to_string());
            },
            FilterType::Date => {
                let values = filter.value.split_once(";").unwrap_or(("", ""));

                sql_values.push(values.0.to_string());
                sql_values.push(values.1.to_string());
            },
        };
    }

    for sql_value in sql_values.iter() {
        sql_params.push(sql_value);
    }

    let mut stmt = conn.prepare(&sql)?;

    sql_params.push(&limit);
    sql_params.push(&offset);

    let rows = stmt.query_map(
        sql_params.as_slice(),
        |row| Ok(serde_rusqlite::from_row::<Operation>(row).expect("Could not deserialize Operation item"))
    )?;

    let mut operations: Vec<Operation> = Vec::new();

    for operation in rows {
        operations.push(operation?);
    }

    stmt.finalize().unwrap();

    Ok(operations)
}

pub(crate) fn find_count(conn: &Connection) -> Box<u32> {
    let mut stmt = conn
        .prepare(
            "
        SELECT
            count(id) as number_of_items
        FROM operations
        WHERE state != :triage
    ",
        )
        .expect("Could not fetch operations");

    let mut result = stmt
        .query(named_params! {
            ":triage": OperationState::PendingTriage.to_string(),
        })
        .unwrap();

    let first_row = result.next().unwrap().unwrap();

    Box::new(serde_rusqlite::from_row::<u32>(first_row).unwrap())
}

pub(crate) fn find_count_triage(conn: &Connection) -> Box<u32> {
    let mut stmt = conn
        .prepare(
            "
        SELECT
            count(id) as number_of_items
        FROM operations
        WHERE state == :triage
    ",
        )
        .expect("Could not fetch operations");

    let mut result = stmt
        .query(named_params! {
            ":triage": OperationState::PendingTriage.to_string(),
        })
        .unwrap();

    let first_row = result.next().unwrap().unwrap();

    Box::new(serde_rusqlite::from_row::<u32>(first_row).unwrap())
}

pub(crate) fn get_by_id(conn: &Connection, id: u32) -> Operation {
    let mut stmt = conn
        .prepare(
            "
        SELECT
            id,
            operation_date,
            type as op_type,
            type_display,
            details,
            amount_in_cents,
            hash,
            state,
            bank_account_id,
            ignored_from_charts,
            (
                SELECT GROUP_CONCAT(tag_id)
                FROM operation_tag
                WHERE operation_id = operations.id
            ) AS tags_ids
        FROM operations
        WHERE id = :id
    ",
        )
        .expect("Could not fetch operation");

    let mut rows = stmt
        .query(named_params! {
            ":id": &id,
        })
        .expect("Could not execute query to fetch an operation by id.");

    let row = rows
        .next()
        .expect("Could not retrieve query rows.")
        .expect("No operation found with this ID.");

    serde_rusqlite::from_row::<Operation>(row).unwrap()
}

pub(crate) fn find_triage(conn: &Connection, page: u16) -> Vec<Operation> {
    let mut stmt = conn
        .prepare(
            "
        SELECT
            id,
            operation_date,
            type as op_type,
            type_display,
            details,
            amount_in_cents,
            hash,
            state,
            bank_account_id,
            ignored_from_charts,
            (
                SELECT GROUP_CONCAT(tag_id)
                FROM operation_tag
                WHERE operation_id = operations.id
            ) AS tags_ids
        FROM operations
        WHERE state = :triage
        ORDER BY operation_date DESC, hash DESC
        LIMIT :limit OFFSET :offset
    ",
        )
        .expect("Could not fetch operations");

    let mut operations: Vec<Operation> = Vec::new();

    let limit = crate::config::NUMBER_PER_PAGE;
    let offset = (page - 1) * limit;

    let mut rows_iter = serde_rusqlite::from_rows::<Operation>(
        stmt.query(named_params! {
            ":triage": OperationState::PendingTriage,
            ":limit": limit.to_string(),
            ":offset": offset.to_string(),
        })
        .unwrap(),
    );

    loop {
        match rows_iter.next() {
            None => break,
            Some(result) => {
                let operation = result.expect("Could not deserialize Operation item");
                operations.push(operation);
            }
        }
    }

    operations
}

pub(crate) fn insert_all(conn: &mut Connection, operations: Vec<Operation>) {
    let mut stmt = conn
        .prepare(
            "
            INSERT INTO operations
            (
                operation_date,
                type,
                type_display,
                details,
                amount_in_cents,
                hash,
                state,
                bank_account_id,
                ignored_from_charts
            )
            VALUES
            (
                :operation_date,
                :type,
                :type_display,
                :details,
                :amount_in_cents,
                :hash,
                :state,
                :bank_account_id,
                :ignored_from_charts
            )
        ",
        )
        .expect("Could not create query to insert operation.");

    for operation in operations.iter() {
        stmt.execute(named_params! {
            ":operation_date": &operation.operation_date,
            ":type": &operation.op_type,
            ":type_display": &operation.type_display,
            ":details": &operation.details,
            ":amount_in_cents": &operation.amount_in_cents,
            ":hash": &operation.hash,
            ":state": &operation.state,
            ":bank_account_id": &operation.bank_account_id,
            ":ignored_from_charts": &operation.ignored_from_charts,
        })
        .expect("Could not insert operation");
    }
}

pub(crate) fn refresh_statuses_with_hashes(conn: &mut Connection) -> u32 {
    let transaction = conn
        .transaction()
        .expect("Could not create database transaction.");

    let result = {
        let mut stmt = transaction
            .prepare(
                "
        UPDATE operations
        SET state = :triage
        WHERE operations.state != :triage
        AND operations.hash IN (
            SELECT t2.hash
            FROM operations AS t2
            GROUP BY t2.hash
            HAVING count(t2.hash) > 1
        )
        ",
            )
            .expect("Could not create query to update operations state.");

        stmt.execute(named_params! {
            ":triage": &OperationState::PendingTriage.to_string(),
        })
        .expect("Could not execute update operations state query")
    };

    transaction
        .commit()
        .expect("Could not execute update operations state transaction");

    result as u32
}

pub(crate) fn update_details(conn: &mut Connection, id: u32, details: String) {
    let mut operation = get_by_id(conn, id);
    let bank_account_slug = bank_accounts::get_slug_by_id(conn, operation.bank_account_id);
    operation.details = details.clone();
    let hash = operation.compute_hash(bank_account_slug);

    conn
        .prepare("UPDATE operations SET details = :details, state = :ok, hash = :hash WHERE id = :id")
        .expect("Could not create query to update operations details.")
        .execute(named_params! {
            ":id": &id,
            ":details": &details,
            ":ok": &OperationState::Ok.to_string(),
            ":hash": &hash,
        })
        .expect("Could not execute update operations details query")
    ;

    conn.prepare("UPDATE operations SET state = :ok WHERE hash = :hash")
        .expect("Could not create query to update operation hash after updating details.")
        .execute(named_params! {
            ":ok": &OperationState::Ok.to_string(),
            ":hash": &hash,
        })
        .expect("Could not execute update operation hash after updating details");
}

pub(crate) fn update_tags(conn: &mut Connection, id: String, tags_ids: Vec<u32>) {
    let mut stmt = conn
        .prepare("DELETE FROM operation_tag WHERE operation_id = :id")
        .expect("Could not create query to update operations details.");

    stmt.execute(named_params! {":id": &id})
    .expect("Could not execute update operations tags query");

    let mut insert_stmt = conn
        .prepare("
                INSERT INTO operation_tag (operation_id, tag_id)
                VALUES (:operation_id, :tag_id)
            ")
        .expect("Could not create query to update operations tags.");

    for tag_id in tags_ids {
        insert_stmt.execute(named_params! {
            ":operation_id": &id,
            ":tag_id": &tag_id,
        })
            .expect("Could not execute insert operation-tag line");
    }
}

pub(crate) fn delete(conn: &mut Connection, id: String) {
    let id_as_number = id.parse::<u32>().unwrap();
    let operation = get_by_id(conn, id_as_number);
    let bank_account_slug = bank_accounts::get_slug_by_id(conn, operation.bank_account_id);
    let hash = operation.compute_hash(bank_account_slug);

    conn.prepare("DELETE FROM operations WHERE id = :id")
        .expect("Could not create query to delete operation.")
        .execute(named_params! {":id": &id})
        .expect("Could not execute delete operation");

    conn.prepare("UPDATE operations SET state = :ok WHERE hash = :hash")
        .expect("Could not create query to update operation hash after delete.")
        .execute(named_params! {
            ":ok": &OperationState::Ok.to_string(),
            ":hash": &hash,
        })
        .expect("Could not execute update operation hash after delete");
}

fn compute_hash(
    op_type: String,
    bank_account_slug: String,
    type_display: String,
    details: String,
    operation_date: String,
    amount_in_cents: i32,
) -> String {
    let base_string = format!(
        "{}_{}_{}_{}_{}_{}",
        op_type,
        bank_account_slug,
        type_display,
        details,
        operation_date,
        amount_in_cents
    );
    let mut hasher = Sha512::new();
    hasher.update(base_string);
    let result = hasher.finalize();
    format!("{:x}", result)
}