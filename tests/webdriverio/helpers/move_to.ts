const base_url = 'https://tauri.localhost';

export default async function moveTo(page: string) {
    let link = await browser.$(`nav.navbar a[href="${page}"]`);
    await expect(link).not.toBe(null);
    await link.click();
    await expect(browser).toHaveUrl(base_url+page);
}
