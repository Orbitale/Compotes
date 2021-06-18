
use rusqlite::Connection;
use rusqlite::NO_PARAMS;
use serde::Serialize;
use serde::Deserialize;
use serde_rusqlite::from_rows;

#[derive(Serialize, Deserialize)]
pub(crate) struct BankAccount
{
    pub(crate) id: u32,
    pub(crate) name: String,
    pub(crate) slug: String,
    pub(crate) currency: String
}

pub(crate) fn create_slug(name: &String) -> String
{
    let str = name.clone();

    str
}

pub(crate) fn find_all(conn: &Connection) -> Vec<BankAccount>
{
    let mut stmt = conn.prepare("
        SELECT
            id,
            name,
            slug,
            currency
        FROM bank_accounts
        ORDER BY name ASC
    ").expect("Could not fetch bank accounts");

    let mut bank_accounts: Vec<BankAccount> = Vec::new();

    let mut rows_iter = from_rows::<BankAccount>(stmt.query(NO_PARAMS).unwrap());

    loop {
        match rows_iter.next() {
            None => { break; },
            Some(bank_account) => {
                let bank_account = bank_account.expect("Could not deserialize BankAccount item");
                bank_accounts.push(bank_account);
            }
        }
    }

    bank_accounts
}

pub(crate) fn save(conn: &Connection, bank_account: BankAccount)
{
    if bank_account.id != 0 {
        let mut stmt = conn.prepare("
            UPDATE bank_acccounts
            SET name = :name,
            currency = :currency
            WHERE id = :id
        ").unwrap();

        stmt.execute_named(&[
            (":id", &bank_account.id),
            (":name", &bank_account.name),
        ]).expect("Could not update tag");
    } else {
        let mut stmt = conn.prepare("
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
        ").expect("An error occured when preparing the SQL statement.");

        stmt.execute_named(&[
            (":name", &bank_account.name),
            (":slug", &create_slug(&bank_account.name)),
            (":currency", &bank_account.currency),
        ]).expect("Could not create bank account");
    }
}
