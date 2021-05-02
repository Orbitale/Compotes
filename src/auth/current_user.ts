import { writable } from "svelte/store";

const isAuthenticatedStore = writable(false);
const userStore = writable({});

let user = null;
userStore.subscribe(storedUser => user = storedUser);

let isAuthenticated = null;
isAuthenticatedStore.subscribe(storedAuthenticationState => isAuthenticated = storedAuthenticationState);

export {
    userStore,
    isAuthenticatedStore,
    user,
    isAuthenticated,
};