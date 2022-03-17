use rusqlite::named_params;
use rusqlite::Connection;
use serde::Deserialize;
use serde::Serialize;
use slugify::slugify;

#[derive(Debug, Serialize, Deserialize)]
pub(crate) struct BankAccount {
    pub(crate) id: u32,
    pub(crate) name: String,
    pub(crate) slug: String,
    pub(crate) currency: String,
}

pub(crate) fn create_slug(name: String) -> String {
    slugify!(&name)
}

pub(crate) fn find_all(conn: &Connection) -> Vec<BankAccount> {
    let mut stmt = conn
        .prepare(
            "
        SELECT
            id,
            name,
            slug,
            currency
        FROM bank_accounts
        ORDER BY id DESC
    ",
        )
        .expect("Could not fetch bank accounts");

    let mut bank_accounts: Vec<BankAccount> = Vec::new();

    let mut rows_iter = serde_rusqlite::from_rows::<BankAccount>(stmt.query([]).unwrap());

    loop {
        match rows_iter.next() {
            None => {
                break;
            }
            Some(bank_account) => {
                let bank_account = bank_account.expect("Could not deserialize BankAccount item");
                bank_accounts.push(bank_account);
            }
        }
    }

    bank_accounts
}

pub(crate) fn create(conn: &Connection, bank_account: BankAccount) -> i64 {
    if bank_account.id != 0 {
        panic!("Cannot create a bank account that already has an ID");
    }

    let mut stmt = conn
        .prepare(
            "
        INSERT INTO bank_accounts (
            id,
            name,
            slug,
            currency
        )
        VALUES (
            null,
            :name,
            :slug,
            :currency
        )
    ",
        )
        .expect("An Error occured when preparing the SQL statement.");

    stmt.execute(named_params! {
        ":name": &bank_account.name.clone(),
        ":slug": &create_slug(bank_account.name.clone()),
        ":currency": &bank_account.currency,
    })
    .expect("Could not create bank account");

    conn.last_insert_rowid()
}

pub(crate) fn update(conn: &Connection, id: u32, name: String, currency: String) {
    let mut stmt = conn
        .prepare("
            UPDATE bank_accounts
            SET
                name = :name,
                currency = :currency
            WHERE id = :id
        ",
        )
        .expect("An Error occured when preparing the SQL statement to update a bank account.");

    stmt.execute(named_params! {
        ":id": &id,
        ":name": &name,
        ":currency": &currency,
    })
    .expect("Could not update bank account");
}
