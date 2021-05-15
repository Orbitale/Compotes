import FieldToDisplay from "./FieldToDisplay.ts";

export default class AssociatedFieldToDisplay extends FieldToDisplay
{
    get associated_field(): null|FieldToDisplay {
        throw new Error('Associated fields cannot be nested.');
    }
}
