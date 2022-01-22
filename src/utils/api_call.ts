import {invoke} from "@tauri-apps/api/tauri";

/**
 * @return Promise<string> with a JSON-serialized version of the expected data.
 */
export default function api_call(command: string, params = {}): Promise<string> {
    if (window.rpc) {
        return invoke(command, params);
    }

    console.error('No API detected.');

    return Promise.resolve('{}');
};
