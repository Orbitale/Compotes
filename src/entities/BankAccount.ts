
export default class BankAccount
{
    public readonly id!: number;
    public name!: String;
    public slug!: String;
    public currency!: String;

    constructor(id: number, name: String, slug: String, currency: String) {
        this.id = id;
        this.name = name;
        this.slug = slug;
        this.currency = currency;
    }

    serialize(): string {
        return JSON.stringify({
            id: this.id,
            name: this.name,
            slug: this.slug,
            currency: this.currency
        });
    }

    clone(): BankAccount {
        return new BankAccount(this.id, this.name, this.slug, this.currency);
    }

    mergeWith(bank_account: BankAccount): void {
        if (bank_account.id.toString() !== this.id.toString()) {
            throw new Error('It is not possible to merge two tag rules that do not share the same ID.');
        }
        this.name = bank_account.name;
        this.slug = bank_account.slug;
        this.currency = bank_account.currency;
    }

    static empty() {
        return new BankAccount(0, "", "", "");
    }
}
