const base_url = 'https://tauri.localhost';

describe('Home page', () => {
    it('should display proper title', async function () {
        let element = await $('main.container');

        await expect(element).not.toBe(null);

        let text = await element.getText();

        await expect(text).toMatch(/Compotes app, new version!/gi);
    });

    it('can display operations list', async function () {
        await browser.url(base_url+'/operations');
        await expect(browser).toHaveUrl(base_url+'/operations');

        let element = await $('h1');
        await expect(element).not.toBe(null);
        let text = await element.getText();
        await expect(text).toMatch(/Operations/gi);
    });
});
