import api_call from "../utils/api_call";
import message from "../utils/message";
import {ToastType} from "./Toast";

export default class OperationsSynchronizer {
    private _syncing: boolean = false;

    get syncing() {
        return this._syncing;
    }

    sync() {
        const _this = this;
        if (this._syncing) {
            return;
        }
        this._syncing = true;
        api_call("sync")
            .then(function (result) {
                const parsedResult = JSON.parse(result);
                const {rules_applied, duplicates_refreshed} = parsedResult;
                if ((typeof rules_applied !== 'undefined') && (typeof duplicates_refreshed !== 'undefined')) {
                    let msg = "Synced!\n" +
                        ((rules_applied > 0)
                            ? `Applied ${rules_applied} tag rules.\n`
                            : "No tag rules to apply.\n") +
                        ((duplicates_refreshed > 0)
                            ? `Detected ${duplicates_refreshed} new duplicates for triage.\n`
                            : "No new duplicate operations.\n")
                    ;
                    message(msg, ToastType.success);
                } else {
                    message('An unknown internal issue has occurred.', ToastType.error);
                }
            })
            .catch(function (error: Error) {
                message(`An error occurred:\n${error.message}`, ToastType.error);
            })
            .finally(function () {
                _this._syncing = false;
            })
        ;
    }
};