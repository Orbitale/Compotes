import { writable } from 'svelte/store';

const defaultConfig = {
	spinLoaderSrc: ''
};

let config = defaultConfig;

export const configStore = writable(defaultConfig);

export function updateConfig(userConfig) {
	config = { ...config, ...userConfig };
	configStore.set(config);
}
