describe('Base application', () => {
    it('should display home page', async function () {
        let element = await $('main.container');

        await expect(element).not.toBe(null);

        let text = await element.getText();

        await expect(text).toMatch(/Compotes app/gi);
    });
});
