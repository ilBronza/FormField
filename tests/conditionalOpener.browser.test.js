const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { test } = require('node:test');
const { chromium } = require('playwright');

const template = fs.readFileSync(path.join(__dirname, '../resources/views/uikit/utilities/_conditionalOpener.blade.php'), 'utf8');
function opener(id) {
    const values = [JSON.stringify('fieldcontainer' + id), '"pdf_show"', '["pdf_title","pdf_sorting_index"]', '"true"', 'false'];
    return template.replace(/@json\((?:[^()\n]|\([^()\n]*\))*\)/g, () => values.shift());
}
function form(id, value) {
    return `<form id="${id}"><fieldset><div class="fieldcontainer fieldcontainer${id}">
        ${['true', 'false', 'null'].map(option => `<input type="radio" name="pdf_show" value="${option}" ${option === value ? 'checked' : ''}>`).join('')}
        </div><div class="fieldcontainer"><textarea name="pdf_title">Titolo conservato</textarea></div>
        <div class="fieldcontainer"><input type="number" name="pdf_sorting_index" value="12"></div>
        <div class="fieldcontainer"><input name="name" value="Sempre visibile"></div></fieldset></form>`;
}

test('conditional opener initializes, toggles both ways and preserves values and other forms', async () => {
    const browser = await chromium.launch({ headless: true, channel: process.env.IB_TEST_BROWSER_CHANNEL || undefined });
    try {
        const page = await browser.newPage();
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        await page.setContent(form('first', 'null') + form('second', 'true') + form('third', 'false'));
        await page.addScriptTag({ path: require.resolve('jquery/dist/jquery.js') });
        for (const id of ['first', 'second', 'third'])
            await page.addScriptTag({ content: opener(id).replace(/<\/?script>/g, '') });

        const title = page.locator('#first [name="pdf_title"]');
        await page.waitForFunction(() => document.querySelector('#first textarea').closest('.fieldcontainer').style.display === 'none');
        assert.equal(await title.isVisible(), false);
        assert.equal(await page.locator('#third [name="pdf_title"]').isVisible(), false);
        assert.equal(await page.locator('#second [name="pdf_title"]').isVisible(), true);

        for (const value of ['true', 'false', 'true', 'null']) {
            await page.locator(`#first [name="pdf_show"][value="${value}"]`).check();
            assert.equal(await title.isVisible(), value === 'true');
            assert.equal(await page.locator('#first [name="pdf_sorting_index"]').isVisible(), value === 'true');
            assert.equal(await page.locator('#first [name="name"]').isVisible(), true);
            assert.equal(await page.locator('#second [name="pdf_title"]').isVisible(), true);
            assert.equal(await title.inputValue(), 'Titolo conservato');
            assert.equal(await page.locator('#first [name="pdf_sorting_index"]').inputValue(), '12');
        }
        assert.deepEqual(errors, []);
    } finally {
        await browser.close();
    }
});
