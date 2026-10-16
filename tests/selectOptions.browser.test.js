const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { test, before, after } = require('node:test');
const { chromium } = require('playwright');

const source = fs.readFileSync(path.join(__dirname, '../resources/assets/js/ilbronza.formfields.select.js'), 'utf8');
let browser;

before(async () => {
    browser = await chromium.launch({ headless: true, channel: process.env.IB_TEST_BROWSER_CHANNEL || undefined });
});

after(async () => {
    if (browser) await browser.close();
});

async function boot(t, html, response = { list: { 0: 'Zero nuovo', 2: 'Due' } }, status = 200)
{
    const page = await browser.newPage();
    t.after(() => page.close());
    const requests = [];
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    t.after(() => assert.deepEqual(errors, []));

    await page.route('http://formfield.test/**', async route => {
        if (route.request().url().includes('/options'))
        {
            requests.push({ method: route.request().method(), url: route.request().url() });
            const json = typeof response === 'function' ? await response(route.request()) : response;
            return route.fulfill({ status: typeof status === 'function' ? status(route.request()) : status, json });
        }

        return route.fulfill({ contentType: 'text/html', body: html });
    });

    await page.goto('http://formfield.test/');
    await page.addScriptTag({ path: require.resolve('jquery/dist/jquery.js') });
    await page.addScriptTag({ path: require.resolve('select2/dist/js/select2.full.js') });
    await page.addScriptTag({ content: `
        window.notifications = [];
        window.saves = 0;
        window.addDangerNotification = message => notifications.push(message);
        jQuery(document).on('change', 'select', () => saves++);
        var module = { exports: {} };
        ${source}
        module.exports(jQuery);
        jQuery('select.select2').select2({ width: '200px', placeholder: 'Scegli' });
    ` });

    return { page, requests };
}

const select = '<select id="field" class="select2" data-possible-values-route="/options"><option value="">Scegli</option><option value="0" selected>Zero</option><option value="1">Vecchio</option></select>';

test('Select2 fetches at each opening, preserves zero, updates labels and never saves', async t => {
    const { page, requests } = await boot(t, select);
    assert.equal(requests.length, 0);
    await page.locator('.select2-selection').click();
    await page.waitForFunction(() => jQuery('#field').select2('isOpen'));
    assert.equal(requests.length, 1);
    assert.equal(requests[0].method, 'GET');
    assert.equal(await page.locator('#field').inputValue(), '0');
    assert.equal(await page.locator('#field option[value="1"]').count(), 0);
    assert.equal(await page.locator('#field option[value="2"]').textContent(), 'Due');
    assert.equal(await page.locator('.select2-selection__rendered').textContent(), 'Zero nuovo');
    assert.equal(await page.evaluate(() => saves), 0);

    await page.evaluate(() => jQuery('#field').select2('close'));
    await page.locator('.select2-selection').click();
    await page.waitForFunction(() => jQuery('#field').select2('isOpen'));
    assert.equal(requests.length, 2);
    assert.notEqual(requests[0].url, requests[1].url);
});

test('concurrent opening attempts share one request and reopen once', async t => {
    let resolve;
    const response = new Promise(done => { resolve = done; });
    const { page, requests } = await boot(t, select, () => response);
    await page.evaluate(() => {
        jQuery('#field').select2('open');
        jQuery('#field').select2('open');
        jQuery('#field').select2('open');
    });
    await page.waitForFunction(() => jQuery('#field').data('ibFormFieldSelectOpening') === true);
    resolve({ list: { 0: 'Zero nuovo', 2: 'Due' } });
    await page.waitForFunction(() => jQuery('#field').select2('isOpen'));
    assert.equal(requests.length, 1);
    assert.equal(await page.locator('#field').inputValue(), '0');
    assert.equal(await page.locator('#field option[value="0"]').textContent(), 'Zero nuovo');
});

test('multiple selections keep only allowed values after a grouped response, with safe labels', async t => {
    const html = '<select id="field" class="select2" multiple data-tag="true" data-possible-values-route="/options"><option value="0" selected>Zero</option><option value="custom" selected>Manuale</option></select>';
    const { page } = await boot(t, html, { groups: { '<Gruppo>': { 0: '<b>Zero</b>', 2: 'Due' } } });
    await page.evaluate(() => window.ibFormFieldReloadSelectOptions(document.querySelector('#field')));
    assert.deepEqual(await page.locator('#field').evaluate(el => Array.from(el.selectedOptions, option => option.value)), ['0']);
    assert.equal(await page.locator('#field option[value="custom"]').count(), 0);
    assert.equal(await page.locator('#field optgroup').getAttribute('label'), '<Gruppo>');
    assert.equal(await page.locator('#field option[value="0"]').textContent(), '<b>Zero</b>');
    assert.equal(await page.locator('#field b').count(), 0);
    assert.equal(await page.evaluate(() => saves), 0);
});

test('native select refreshes on pointer and opening keys, with placeholder and manual input intact', async t => {
    const html = '<select id="field" data-possible-values-route="/options"><option value="" selected disabled>Scegli</option><option value="1">Vecchio</option><option class="ibuseselectmanualinput">Manual input</option></select>';
    const { page, requests } = await boot(t, html, { 2: 'Due' });
    await page.locator('#field').dispatchEvent('mousedown', { button: 0 });
    await page.waitForFunction(() => !!document.querySelector('#field option[value="2"]'));
    assert.equal(requests.length, 1);
    assert.equal(await page.locator('#field').inputValue(), '');
    assert.equal(await page.locator('#field option[value=""]').isDisabled(), true);
    assert.equal(await page.locator('#field option.ibuseselectmanualinput').count(), 1);
    await page.locator('#field').dispatchEvent('keydown', { key: 'Tab' });
    await page.locator('#field').dispatchEvent('mousedown', { button: 2 });
    assert.equal(requests.length, 1);
    const response = page.waitForResponse(response => response.url().includes('/options'));
    await page.locator('#field').dispatchEvent('keydown', { key: 'ArrowDown' });
    await response;
    assert.equal(requests.length, 2);
    assert.equal(await page.evaluate(() => saves), 0);
});

test('HTTP errors leave the list and selection intact and allow a retry', async t => {
    const { page, requests } = await boot(t, select, { message: 'Errore' }, 500);
    await page.locator('.select2-selection').click();
    await page.waitForFunction(() => notifications.length === 1 && !jQuery('#field').data('ibFormFieldSelectOpening'));
    assert.equal(await page.locator('#field option').count(), 3);
    assert.equal(await page.locator('#field').inputValue(), '0');
    await page.locator('.select2-selection').click();
    await page.waitForFunction(() => notifications.length === 2);
    assert.equal(requests.length, 2);
});

test('malformed option lists never partially replace the old list', async t => {
    const { page } = await boot(t, select, { list: { 2: 'Due', 3: { invalid: true } } });
    const message = await page.evaluate(async () => {
        try { await window.ibFormFieldReloadSelectOptions(document.querySelector('#field')); }
        catch (error) { return error.message; }
    });
    assert.match(message, /Etichetta non valida/);
    assert.equal(await page.locator('#field option').count(), 3);
    assert.equal(await page.locator('#field option[value="2"]').count(), 0);
    assert.equal(await page.locator('#field').inputValue(), '0');
});

test('an empty server list clears old options and the incompatible selection', async t => {
    const { page } = await boot(t, select, { list: [] });
    await page.evaluate(() => window.ibFormFieldReloadSelectOptions(document.querySelector('#field')));
    assert.equal(await page.locator('#field option').count(), 1);
    assert.equal(await page.locator('#field').inputValue(), '');
});

test('unconfigured, readonly and disabled selects do not fetch', async t => {
    const html = '<select id="plain"><option>Uno</option></select><select readonly data-possible-values-route="/options"><option>Uno</option></select><select disabled data-possible-values-route="/options"><option>Uno</option></select>';
    const { page, requests } = await boot(t, html);
    await page.evaluate(() => document.querySelectorAll('select').forEach(el => {
        jQuery(el).trigger(jQuery.Event('mousedown', { button: 0 }));
        if (window.ibFormFieldReloadSelectOptions(el) !== null) throw new Error('Richiesta inattesa');
    }));
    assert.equal(requests.length, 0);
});

test('delegated handlers also refresh selects inserted after initialization', async t => {
    const { page, requests } = await boot(t, '<div id="container"></div>');
    await page.evaluate(html => jQuery('#container').append(html).find('select').select2(), select);
    await page.locator('.select2-selection').click();
    await page.waitForFunction(() => jQuery('#field').select2('isOpen'));
    assert.equal(requests.length, 1);
});

test('reciprocal dependencies use current form values, keep compatible selections and clear incompatible ones without loops', async t => {
    const html = `<form>
        <select id="operator" name="operator_id" class="select2" data-refresh-select-options='["task_id"]'
            data-possible-values-route="/options/operators" data-possible-values-route-fields='["task_id"]'>
            <option value=""></option><option value="1" selected>Uno</option><option value="2">Due</option>
        </select>
        <select id="task" name="task_id" class="select2" data-refresh-select-options='["operator_id"]'
            data-possible-values-route="/options/tasks" data-possible-values-route-fields='["operator_id"]'>
            <option value=""></option><option value="t1" selected>Prima</option>
        </select>
    </form>`;
    const { page, requests } = await boot(t, html, request => {
        const url = new URL(request.url());
        return url.pathname.endsWith('/operators') ? { list: { 2: 'Due aggiornato' } }
            : { list: url.searchParams.get('operator_id') === '2' ? { t1: 'Prima', t2: 'Seconda' } : { t3: 'Terza' } };
    });

    await page.evaluate(() => jQuery('#operator').val('2').trigger('change'));
    await page.waitForFunction(() => !!document.querySelector('#task option[value="t2"]'));
    assert.equal(await page.locator('#task').inputValue(), 't1');

    await page.evaluate(() => jQuery('#task').val('t2').trigger('change'));
    await page.waitForFunction(() => jQuery('#operator option[value="2"]').text() === 'Due aggiornato');
    assert.equal(await page.locator('#operator').inputValue(), '2');

    await page.evaluate(() => jQuery('#operator').append(new Option('Uno', '1')).val('1').trigger('change'));
    await page.waitForFunction(() => !!document.querySelector('#task option[value="t3"]'));
    assert.equal(await page.locator('#task').inputValue(), '');
    assert.equal(await page.locator('#operator').inputValue(), '1');
    assert.equal(requests.length, 3);
    assert.equal(new URL(requests[0].url).searchParams.get('operator_id'), '2');
    assert.equal(new URL(requests[1].url).searchParams.get('task_id'), 't2');
    assert.equal(new URL(requests[2].url).searchParams.get('operator_id'), '1');
    assert.equal(await page.evaluate(() => saves), 3);
});

test('a text change refreshes five selects with text, date, checkbox, radio and multiple context values', async t => {
    const context = JSON.stringify(['search', 'starts_at', 'active', 'roles', 'status', 'groups', 'empty', 'empty_multiple', 'zero']);
    const targets = ['a', 'b', 'c', 'd', 'e'];
    const html = `<form>
        <input id="search" name="search" value="old" data-refresh-select-options='${JSON.stringify(targets)}'>
        <input name="starts_at" type="date" value="2026-10-15">
        <input name="active" type="checkbox">
        <input name="roles[]" type="checkbox" value="r1" checked><input name="roles[]" type="checkbox" value="r2">
        <input name="status" type="radio" value="open" checked><input name="status" type="radio" value="closed">
        <select name="groups[]" multiple><option value="0" selected>Zero</option><option value="2" selected>Due</option></select>
        <input name="empty" value=""><input name="zero" value="0"><select name="empty_multiple[]" multiple></select>
        ${targets.map(name => `<select name="${name}" data-possible-values-route="/options/${name}" data-possible-values-route-fields='${context}'><option value="old" selected>Vecchio</option></select>`).join('')}
    </form>`;
    const { page, requests } = await boot(t, html, { list: { ok: 'Nuovo' } });
    await page.evaluate(() => jQuery('#search').val('new').trigger('input'));
    assert.equal(requests.length, 0);
    await page.evaluate(() => jQuery('#search').trigger('change'));
    await page.waitForFunction(() => document.querySelectorAll('option[value="ok"]').length === 5);
    assert.equal(requests.length, 5);
    for (const request of requests)
    {
        const params = new URL(request.url).searchParams;
        assert.equal(params.get('search'), 'new');
        assert.equal(params.get('starts_at'), '2026-10-15');
        assert.equal(params.get('active'), 'false');
        assert.deepEqual(params.getAll('roles[]'), ['r1']);
        assert.equal(params.get('status'), 'open');
        assert.deepEqual(params.getAll('groups[]'), ['0', '2']);
        assert.equal(params.get('empty'), '');
        assert.deepEqual(params.getAll('empty_multiple[]'), ['']);
        assert.equal(params.get('zero'), '0');
    }
    assert.deepEqual(await page.locator('select[data-possible-values-route]').evaluateAll(fields => fields.map(field => field.selectedIndex)), [-1, -1, -1, -1, -1]);
    assert.equal(await page.evaluate(() => saves), 0);
});

test('fields with the same names in different forms do not interfere', async t => {
    const html = ['first', 'second'].map(id => `<form id="${id}">
        <input name="search" value="${id}" data-refresh-select-options='["target"]'>
        <select name="target" data-possible-values-route="/options" data-possible-values-route-fields='["search"]'><option value="old" selected>Vecchio</option></select>
    </form>`).join('');
    const { page, requests } = await boot(t, html, { list: { new: 'Nuovo' } });
    await page.evaluate(() => jQuery('#second input').val('edited').trigger('change'));
    await page.waitForFunction(() => !!document.querySelector('#second option[value="new"]'));
    assert.equal(requests.length, 1);
    assert.equal(new URL(requests[0].url).searchParams.get('search'), 'edited');
    assert.equal(await page.locator('#first select').inputValue(), 'old');
    assert.equal(await page.locator('#second select').inputValue(), '');
});

test('repeatable fields resolve targets and context within the matching rendered index', async t => {
    const html = `<form>${['r1', 'r2'].map(id => `
        <input id="source-${id}" name="search[${id}]" data-flatname="search" value="${id}" data-refresh-select-options='["target"]'>
        <select id="target-${id}" name="target[${id}]" data-flatname="target" data-possible-values-route="/options" data-possible-values-route-fields='["search"]'><option value="old" selected>Vecchio</option></select>
    `).join('')}</form>`;
    const { page, requests } = await boot(t, html, { list: { new: 'Nuovo' } });
    await page.evaluate(() => jQuery('#source-r2').val('edited').trigger('change'));
    await page.waitForFunction(() => !!document.querySelector('#target-r2 option[value="new"]'));
    assert.equal(requests.length, 1);
    assert.equal(new URL(requests[0].url).searchParams.get('search'), 'edited');
    assert.equal(await page.locator('#target-r1').inputValue(), 'old');
});

for (const staleStatus of [200, 500])
test('newer source changes win over obsolete HTTP ' + staleStatus + ' responses arriving later', async t => {
    let releaseOld, releaseNew, sawOld, sawNew;
    const oldResponse = new Promise(resolve => { releaseOld = resolve; });
    const newResponse = new Promise(resolve => { releaseNew = resolve; });
    const oldSeen = new Promise(resolve => { sawOld = resolve; });
    const newSeen = new Promise(resolve => { sawNew = resolve; });
    const html = `<form><input id="search" name="search" data-refresh-select-options='["target"]'>
        <select id="target" name="target" data-possible-values-route="/options" data-possible-values-route-fields='["search"]'><option value="old" selected>Vecchio</option></select></form>`;
    const { page, requests } = await boot(t, html, request => {
        if (new URL(request.url()).searchParams.get('search') === 'old') { sawOld(); return oldResponse; }
        sawNew(); return newResponse;
    }, request => new URL(request.url()).searchParams.get('search') === 'old' ? staleStatus : 200);
    await page.evaluate(() => {
        const reload = window.ibFormFieldReloadSelectOptions;
        window.loads = [];
        window.ibFormFieldReloadSelectOptions = field => {
            const result = reload(field);
            loads.push(result.then(() => 'applied', error => error.name));
            return result;
        };
        jQuery('#search').val('old').trigger('change');
    });
    await oldSeen;
    await page.evaluate(() => jQuery('#search').val('new').trigger('change'));
    await newSeen;
    releaseNew({ list: { latest: 'Nuovo' } });
    await page.waitForFunction(() => !!document.querySelector('#target option[value="latest"]'));
    releaseOld({ list: { obsolete: 'Superato' } });
    assert.deepEqual(await page.evaluate(() => Promise.all(loads)), ['StaleSelectOptionsError', 'applied']);
    assert.equal(await page.locator('#target option[value="obsolete"]').count(), 0);
    assert.equal(requests.length, 2);
    assert.deepEqual(await page.evaluate(() => notifications), []);
});

test('a source edit invalidates an in-flight response even without a configured refresh trigger', async t => {
    let release, observed;
    const response = new Promise(resolve => { release = resolve; });
    const seen = new Promise(resolve => { observed = resolve; });
    const html = `<form><input id="search" name="search" value="old">
        <select id="target" name="target" data-possible-values-route="/options" data-possible-values-route-fields='["search"]'><option value="old" selected>Vecchio</option></select></form>`;
    const { page } = await boot(t, html, () => { observed(); return response; });
    await page.evaluate(() => { window.load = ibFormFieldReloadSelectOptions(document.querySelector('#target')).catch(error => error.name); });
    await seen;
    await page.evaluate(() => jQuery('#search').val('new'));
    release({ list: { obsolete: 'Superato' } });
    assert.equal(await page.evaluate(() => window.load), 'StaleSelectOptionsError');
    assert.equal(await page.locator('#target').inputValue(), 'old');
    assert.equal(await page.locator('#target option[value="obsolete"]').count(), 0);
});

test('missing or ambiguous context is reported and never read from another form', async t => {
    const html = `<form><input name="trigger" data-refresh-select-options='["target"]'>
        <select name="target" data-possible-values-route="/options" data-possible-values-route-fields='["missing"]'><option value="old" selected>Vecchio</option></select></form>
        <form><input name="missing" value="foreign"></form>`;
    const { page, requests } = await boot(t, html);
    await page.evaluate(() => jQuery('input[name="trigger"]').trigger('change'));
    await page.waitForFunction(() => notifications.length === 1);
    assert.equal(requests.length, 0);
    assert.equal(await page.locator('select').inputValue(), 'old');
    await page.evaluate(() => {
        jQuery('form').first().append('<input name="missing" value="a"><input name="missing" value="b">');
        jQuery('input[name="trigger"]').trigger('change');
    });
    await page.waitForFunction(() => notifications.length === 2);
    assert.equal(requests.length, 0);
});
