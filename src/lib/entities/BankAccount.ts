import type Entity from '$lib/struct/Entity';

export default class BankAccount implements Entity {
	public id!: number;
	public name!: string;
	public slug!: string;
	public currency!: string;

	constructor(id: number, name: string, slug: string, currency: string) {
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

	static fromObject(object: {[key: string]: any}): BankAccount {
		if ((object.id && isNaN(object.id)) || !object.name || !object.slug || !object.currency) {
			throw new Error('Invalid object: cannot create a BankAccount.');
		}

		return new BankAccount(Number(object.id)||0, object.name, object.slug, object.currency);
	}

	public setId(id: number) {
		if (!id) {
			throw new Error('Cannot set an empty ID on an object.');
		}
		if (this.id > 0) {
			throw new Error('Cannot set an ID on an object that already has one.');
		}
		this.id = id;
	}

	static empty() {
		return new BankAccount(0, '', '', '');
	}
}
