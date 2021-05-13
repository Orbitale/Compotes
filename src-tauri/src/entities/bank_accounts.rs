
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
