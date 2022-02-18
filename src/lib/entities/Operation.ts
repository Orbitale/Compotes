import type BankAccount from "./BankAccount";
import sha512 from "$lib/utils/sha512";
import {DateFormat, dateFormatToRegex, NormalizedDate} from "$lib/utils/date";
import type Tag from "./Tag";

export enum OperationState {
    ok = "ok",
    pending_triage = "pending_triage",
}

export default class Operation
{
    public readonly id!: number;
    public readonly operation_date!: string;
    public readonly op_type!: string;
    public readonly type_display!: string;
    public readonly details!: string;
    public readonly amount_in_cents!: number;
    public readonly amount!: number;
    public hash!: string;
    public readonly state!: OperationState;
    public readonly ignored_from_charts!: boolean;
    public readonly bank_account_id: number;
    public readonly bank_account: BankAccount;
    public readonly tags: Array<Tag>;
    public readonly tags_ids: Array<number>;

    constructor(
        id: number,
        operation_date: string,
        op_type: string,
        type_display: string,
        details: string,
        amount_in_cents: number,
        state: OperationState,
        ignored_from_charts: boolean,
        bank_account: BankAccount,
        hash: string,
        tags: Array<Tag>,
    ) {
        this.id = id;
        this.operation_date = operation_date;
        this.op_type = op_type;
        this.type_display = type_display;
        this.details = details;
        this.amount_in_cents = amount_in_cents;
        this.amount = amount_in_cents / 100;
        this.state = state;
        this.ignored_from_charts = ignored_from_charts;
        this.bank_account = bank_account;
        this.bank_account_id = bank_account.id;
        this.hash = hash || '';
        this.tags = tags;
        this.tags_ids = tags.map((tag: Tag) => tag.id);
    }

    get date() {
        let date = new Date(this.operation_date.toString());

        return date.toLocaleDateString();
    }

    get amount_display() {
        return this.amount.toLocaleString() + ' ' + this.bank_account.currency;
    }

    public static normalizeAmount(amount: string): number {
        const normalized = parseInt(amount.replace(/[^0-9-]+/gi, ''), 10);
        if (isNaN(normalized)) {
            throw new Error(`Could not normalize amount "${amount}".\nIt does not seem to be a valid number.`);
        }

        return normalized;
    }

    public static normalizeDate(dateString: string, dateFormat: DateFormat): null|NormalizedDate {
        const matches = dateFormatToRegex(dateFormat).exec(dateString);

        if (!matches) {
            throw new Error(`Date "${dateString}" does not respect the "${dateFormat}" date format.\nPlease double-check the date format field.`);
        }

        const parsedDate = Date.parse(matches.groups.year + '-' + matches.groups.month + '-' + matches.groups.day + "T00:00:00.000Z");
        if (isNaN(parsedDate)) {
            throw new Error(`Could not parse date "${dateString}".\nIt does not seem to be a valid date.\nPlease check the date format option.`)
        }
        const dateObject = new Date(parsedDate);

        return new NormalizedDate(dateObject);
    }

    public async sync(): Promise<void> {
        await this.recomputeHash();
    }

    public async recomputeHash() {
        this.hash = await Operation.computeHash(
            this.op_type,
            this.bank_account.slug,
            this.type_display,
            this.details,
            this.operation_date,
            this.amount_in_cents,
        );
    }

    public static async computeHash(
        op_type: string,
        bank_account_slug: string,
        type_display: string,
        details: string,
        operation_date: string,
        amount_in_cents: number,
    ): Promise<string> {
        const str =
            op_type+
            '_'+bank_account_slug+
            '_'+type_display+
            '_'+details+
            '_'+operation_date+
            '_'+amount_in_cents
        ;

        return await sha512(str);
    }

    public serialize() {
        return JSON.stringify({
            id: this.id,
            operation_date: this.operation_date,
            op_type: this.op_type,
            type_display: this.type_display,
            details: this.details,
            amount_in_cents: this.amount_in_cents,
            state: this.state,
            ignored_from_charts: this.ignored_from_charts,
            bank_account_id: this.bank_account_id,
            hash: this.hash,
            tags_ids: this.tags_ids,
        }, null, 4);
    }
}
