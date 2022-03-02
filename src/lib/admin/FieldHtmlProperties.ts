export default class FieldHtmlProperties {
    public readonly html_class: string;

    constructor(html_class: string = '') {
        this.html_class = html_class;
    }

    static defaults() {
        return new FieldHtmlProperties();
    }
}