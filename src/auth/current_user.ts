// @ts-ignore
import User from "../struct/User.ts";
import {writable, get} from "svelte/store";
import {replace} from 'svelte-spa-router';

const userStore = writable(null);

const getUser = (): User | null => {
    // TODO: Use this later: get(userStore);
    return new User("1", "admin", "admin");
};

const needsUser = async (): Promise<void> => {
    const user = getUser();

    if (!user || !user.id) {
        await replace('#/');
    }
};

const setUser = (user: User | null): void => userStore.set(user);

export {
    getUser,
    setUser,
    needsUser,
};
