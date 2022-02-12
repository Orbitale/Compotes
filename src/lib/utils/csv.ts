
export enum CsvFieldReference {
    DATE = '📅 Date',
    TYPE = '🏷 Type',
    TYPE_DISPLAY = '🔖 Type (display)',
    DETAILS = '✏ Details',
    AMOUNT = '💰 Amount',
}

export function referenceToEntityProperty(ref) {
    for (let key of Object.keys(CsvFieldReference)) {
        if (!CsvFieldReference.hasOwnProperty(key)) continue;
        if (CsvFieldReference[key] === ref) {
            return key;
        }
    }
    throw `Invalid csv field ${ref}.`;
}
