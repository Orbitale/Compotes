import {writable} from 'svelte/store';

class Config {
	public spinLoaderSrc: string = '';
	public builtinFilters: string = {};
}

let config = new Config();

export const configStore = writable(config);

export function updateConfig(userConfig) {
	for (const configKey in userConfig) {
		config[configKey] = userConfig[configKey];
	}

	configStore.set(config);
}

export function getConfig(): Config {
	return config;
}
