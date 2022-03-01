import moveTo from "../helpers/test_helpers";

describe('Tag rules page', () => {
    it('can display tag rules list', async function () {
        await moveTo("/tag-rules");

        let h1 = await $('h1');
        await expect(h1).not.toBe(null);
        let h1Text = await h1.getText();
        await expect(h1Text).toMatch(/Tag rules/gi);

        let table = await $('table.table');
        await expect(table).not.toBe(null);
    });

    it('can add tag rule', async function () {
        await moveTo("/tag-rules");
        await moveTo("/tag-rules/new");

        let input = await $("input#matching_pattern");
        await input.setValue("test");

        let select = await $("select#tags");
        // TODO
    });
});
