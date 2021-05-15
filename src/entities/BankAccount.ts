
export default class BankAccount
{
    public readonly id!: number;
    public readonly name!: String;
    public readonly slug!: String;
    public readonly currency!: String;

    constructor(id: number, name: String, slug: String, currency: String) {
        this.id = id;
        this.name = name;
        this.slug = slug;
        this.currency = currency;
    }
}
