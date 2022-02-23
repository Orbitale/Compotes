import {spawn} from 'child_process';
import * as path from 'path';

let tauriDriver;

const binSuffix = process.platform === 'win32' ? '.exe' : '';

exports.config = {

    autoCompileOpts: {
        autoCompile: true,
        tsNodeOpts: {
            transpileOnly: true,
            project: './tsconfig.json'
        }
    },

    specs: [
        './specs/**/*.ts'
    ],
    maxInstances: 1,

    capabilities: [
        {
            "tauri:options": {
                application: "../../src-tauri/target/release/compotes"+binSuffix,
            },
            "ms:edgeOptions": {
                args: ["headless"]
            },
            maxInstances: 1,
            acceptInsecureCerts: true
        }
    ],

    // Level of logging verbosity: trace | debug | info | warn | error | silent
    logLevel: 'warn',

    waitforTimeout: 10000,
    connectionRetryTimeout: 120000,
    connectionRetryCount: 3,

    // see also: https://webdriver.io/docs/frameworks
    framework: 'mocha',

    reporters: [
        'spec',
    ],

    // See the full list at http://mochajs.org/
    mochaOpts: {
        ui: 'bdd',
        timeout: 60000
    },
    beforeSession: function (config, capabilities, specs, cid) {
        tauriDriver = spawn(
            path.resolve(process.cwd(), '../../bin/tauri-driver'+binSuffix),
            [],
            { stdio: [null, process.stdout, process.stderr] }
        );
    },
    afterSession: function () {
        tauriDriver.kill();
    }
}
