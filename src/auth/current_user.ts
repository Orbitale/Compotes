import { writable, get } from "svelte/store";
import type User from "../struct/User.ts";

const userStore = writable(null);

const getUser = (): User|null => get(userStore);
const setUser = (user: User|null): void => userStore.set(user);

export {
    getUser,
    setUser,
};