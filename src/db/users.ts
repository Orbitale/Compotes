import User from "./../struct/User.ts";

export const users = [
    new User('fbcd366d-29b9-4e78-a295-82dc3575143e', 'admin', 'admin'),
];

export function get_by_id(id: string): User|null
{
    for (const user of users) {
        if (user.id === id) {
            return user;
        }
    }

    return null;
}
