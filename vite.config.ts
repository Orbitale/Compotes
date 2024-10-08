import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import path from 'path';
import fs from 'fs';

const copyFile = function (options) {
    return function () {
        const targetDir = path.dirname(options.target);
        if (!fs.existsSync(targetDir)){
            fs.mkdirSync(targetDir);
        }
        fs.writeFileSync(options.target, fs.readFileSync(options.source));
        console.info(` > Copied ${options.source} to ${options.target}.`);
    };
}

/** @type {import('vite').UserConfig} */
export default defineConfig({
    hmr: false,
    plugins: [
        copyFile({
            source:  './node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
            target: './static/bootstrap.bundle.min.js',
        }),
        copyFile({
            source:  './node_modules/bootstrap/dist/js/bootstrap.bundle.min.js.map',
            target: './static/bootstrap.bundle.min.js.map',
        }),
        sveltekit(),
    ],
});
