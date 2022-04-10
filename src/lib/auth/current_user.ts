import User from '$lib/struct/User';
import { writable } from 'svelte/store';

const userStore = writable(null);

const getUser = async (): Promise<User | null> => {
	// TODO: Use this later: get(userStore);
	return Promise.resolve(new User('1', 'admin', 'admin'));
};

const needsUser = async (): Promise<void> => {
	const user = await getUser();

	if (!user || !user.id) {
		location.href = '/';
	}
};

const setUser = (user: User | null): void => userStore.set(user);

export { getUser, setUser, needsUser };
