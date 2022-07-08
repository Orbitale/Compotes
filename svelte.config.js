import adapter from '@sveltejs/adapter-static';
import preprocess from 'svelte-preprocess';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	// Consult https://github.com/sveltejs/svelte-preprocess
	// for more information about preprocessors
	preprocess: preprocess(),

	kit: {
		adapter: adapter({
			pages: "src-tauri/target/frontend-build",
			assets: "src-tauri/target/frontend-build",
		}),

		prerender: {
			default: true,
		},
	},
};

export default config;
