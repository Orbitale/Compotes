let crypto;

export default function random_bytes(bytes: number): string {
	if (!crypto) {
		crypto = window.crypto;
	}
	return crypto.getRandomValues(new Uint8Array(bytes)).join('');
}
