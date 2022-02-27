
describe('Home page', () => {
    it('should display proper title', async function () {
        let element = await $('main.container');

        await expect(element).not.toBe(null);

        let text = await element.getText();

        await expect(text).toMatch(/Compotes app, new version!/gi);
    });
});
