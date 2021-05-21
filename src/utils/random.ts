export default function random_bytes(bytes: number): string {
    return crypto.getRandomValues(new Uint8Array(bytes)).join('');
}