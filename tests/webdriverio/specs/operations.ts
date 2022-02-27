import moveTo from "../helpers/move_to";

describe('Operations page', () => {
    it('can display operations list', async function () {
        await moveTo("/operations");

        let h1 = await $('h1');
        await expect(h1).not.toBe(null);
        let h1Text = await h1.getText();
        await expect(h1Text).toMatch(/Operations/gi);

        let table = await $('table.table');
        await expect(table).not.toBe(null);
    });
});
