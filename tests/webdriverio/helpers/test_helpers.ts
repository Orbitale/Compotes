const base_url = process.platform === 'win32' ? 'https://tauri.localhost' : 'tauri://localhost';

export default async function moveTo(page: string) {
    let link = await browser.$(`a[href="${page}"]`);
    await expect(link).not.toBe(null);
    await link.click();
    await expect(browser).toHaveUrl(base_url+page);
}
