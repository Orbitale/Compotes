import adapter from '@sveltejs/adapter-static';
import preprocess from 'svelte-preprocess';
import path from 'path';
import fs from 'fs';

const copyFile = function (options) {
	return function () {
		const targetDir = path.dirname(options.target);
		if (!fs.existsSync(targetDir)){
			fs.mkdirSync(targetDir);
		}
		fs.writeFileSync(options.target, fs.readFileSync(options.source));
	};
}

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

		vite: {
			prebundleSvelteLibraries: true,
			plugins: [
				copyFile({
					source:  './node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
					target: './static/bootstrap.min.js',
				}),
				copyFile({
					source:  './node_modules/bootstrap/dist/js/bootstrap.bundle.min.js.map',
					target: './static/bootstrap.min.js.map',
				}),
			],
			ssr: {
				noExternal: [ 'dayjs' ]
			}
		}
	},
};

export default config;
