import type BankAccount from "./BankAccount.ts";
import {getBankAccountById} from "../db/bank_accounts.ts";

enum OperationState {
    ok = "ok",
    pending_triage = "pending_triage",
}

export default class Operation
{
    public readonly id!: number;
    public readonly operation_date!: String;
    public readonly op_type!: String;
    public readonly type_display!: String;
    public readonly details!: String;
    public readonly amount_in_cents!: number;
    public readonly amount!: number;
    public readonly hash!: String;
    public readonly state!: OperationState;
    public readonly ignored_from_charts!: boolean;
    public readonly bank_account_id!: number;

    public readonly bank_account: BankAccount|null = null;

    constructor(id: number, operation_date: String, op_type: String, type_display: String, details: String, amount_in_cents: number, hash: String, state: OperationState, ignored_from_charts: boolean, bank_account_id: number) {
        this.id = id;
        this.operation_date = operation_date;
        this.op_type = op_type;
        this.type_display = type_display;
        this.details = details;
        this.amount_in_cents = amount_in_cents;
        this.amount = amount_in_cents / 100;
        this.hash = hash;
        this.state = state;
        this.ignored_from_charts = ignored_from_charts;
        this.bank_account_id = bank_account_id;
    }

    get date() {
        let date = new Date(this.operation_date.toString());

        return date.toLocaleDateString();
    }

    get amount_display() {
        if (!this.bank_account) {
            throw new Error('Cannot display Operation amount if no bank account is present.');
        }

        return this.amount.toLocaleString() + ' ' + this.bank_account.currency;
    }

    public async sync(): Promise<void> {
        await this.fetch_bank_account();
    }

    public async fetch_bank_account(): Promise<BankAccount> {
        if (this.bank_account) {
            return this.bank_account;
        }

        const bank_account = await getBankAccountById(this.bank_account_id.toString());

        if (!bank_account) {
            throw new Error(`No bank account with id ${this.bank_account_id}`);
        }

        this.bank_account = bank_account;

        return bank_account;
    }
}
