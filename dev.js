import {spawn} from "child_process";

const yarn_path = process.env.npm_execpath;
const node_path = process.env.npm_node_execpath;

const create_process = function(name, args) {
    let child = spawn(node_path, [yarn_path, ...args]);

    child.stdout.on('data', data => process.stdout.write(`${data}`));
    child.stderr.on('data', data => process.stderr.write(`${data}`));
    child.on('close', (code) => console.log(`child process ${name} exited with code ${code}`));

    return child;
}

create_process('FRONTEND', ['run', 'dev']);
create_process('BACKEND', ['run', 'tauri', 'dev']);
