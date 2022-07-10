export default class GraphDataset {
	// Plot name
	public readonly label: string;
	public readonly data: Array<string | Number>;

	public readonly backgroundColor: string;
	public readonly borderColor: string;
	public readonly borderWidth: number;

	constructor(
		label: string,
		data: Array<string | Number>,
		backgroundColor: string,
		borderColor: string,
		borderWidth: number
	) {
		this.label = label;
		this.data = data;
		this.backgroundColor = backgroundColor;
		this.borderColor = borderColor;
		this.borderWidth = borderWidth;
	}
}
