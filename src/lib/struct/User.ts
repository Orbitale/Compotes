export default class User {
	private readonly _id: string;
	private readonly _username: string;
	private readonly _password: string;

	constructor(id: string, username: string, password: string) {
		this._id = id;
		this._username = username;
		this._password = password;
	}

	get id(): string {
		return this._id;
	}

	get username(): string {
		return this._username;
	}

	get password(): string {
		return this._password;
	}

	isValidPassword(input_password: string) {
		return this._password === input_password;
	}
}
