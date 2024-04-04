import adapter from '@sveltejs/adapter-static';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	// Consult https://kit.svelte.dev/docs/integrations#preprocessors
	// for more information about preprocessors
	preprocess: vitePreprocess(),

	kit: {
		adapter: adapter({
			pages: "src-tauri/target/frontend-build",
			assets: "src-tauri/target/frontend-build",
			precompress: true,
			fallback: 'index.html',
			prerender: {
				crawl: true,
			},
		}),
	},
};

export default config;
