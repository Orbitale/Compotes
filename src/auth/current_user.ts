import { writable } from "svelte/store";

const isAuthenticatedStore = writable(false);
const userStore = writable({});

export {
    userStore,
    isAuthenticatedStore,
};