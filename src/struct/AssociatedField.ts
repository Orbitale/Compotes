import Field from "./Field.ts";

export default class AssociatedField extends Field
{
    get associated_field(): null|Field {
        throw new Error('Associated fields cannot be nested.');
    }
}
