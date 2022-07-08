import { sveltekit } from '@sveltejs/kit/vite';
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

/** @type {import('vite').UserConfig} */
const config = {
    prebundleSvelteLibraries: true,
    optimizeDeps: {
        include: [],
    },
    plugins: [
        sveltekit(),
        copyFile({
            source:  './node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
            target: './static/bootstrap.min.js',
        }),
        copyFile({
            source:  './node_modules/bootstrap/dist/js/bootstrap.bundle.min.js.map',
            target: './static/bootstrap.min.js.map',
        }),
    ],
};

export default config;
