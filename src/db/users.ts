// @ts-ignore
import User from '../struct/User.ts';

const users = [
    new User('fbcd366d-29b9-4e78-a295-82dc3575143e', 'admin', 'admin'),
];

export function getUsers() {
    return users;
}

export function getUserById(id: string): User | null {
    for (const user of users) {
        if (user.id === id) {
            return user;
        }
    }

    return null;
}
