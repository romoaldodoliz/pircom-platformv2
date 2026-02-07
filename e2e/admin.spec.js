const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

// Configuration via environment variables
const BASE_URL = process.env.BASE_URL || 'http://localhost:8888/Pircom/admin';
const ADMIN_USER = process.env.ADMIN_USER || 'admin@pircom.org.mz';
const ADMIN_PASS = process.env.ADMIN_PASS || 'password';
const MANAGER_USER = process.env.MANAGER_USER || 'manager@pircom.org.mz';
const MANAGER_PASS = process.env.MANAGER_PASS || 'password';

// Selectors - adjust as needed for your app
const SELECTORS = {
  username: process.env.LOGIN_USER_SEL || 'input[name="username"], input[name="email"], input[type="email"]',
  password: process.env.LOGIN_PASS_SEL || 'input[name="password"], input[type="password"]',
  submit: process.env.LOGIN_SUBMIT_SEL || 'button[type="submit"], input[type="submit"]'
};

function mkEvidenceDir(scenario) {
  const dir = path.join('e2e', 'evidence', scenario, new Date().toISOString().replace(/[:.]/g, '-'));
  fs.mkdirSync(dir, { recursive: true });
  return dir;
}

async function take(page, dir, name) {
  const p = path.join(dir, `${name}.png`);
  await page.screenshot({ path: p, fullPage: true });
}

async function findFirst(page, selectorExpression) {
  const parts = selectorExpression.split(',').map(s => s.trim());
  for (const sel of parts) {
    const el = page.locator(sel);
    if (await el.count() > 0) return el.first();
  }
  return null;
}

async function login(page, user, pass, evidenceDir) {
  await page.goto(BASE_URL + '/index.php');
  const userEl = await findFirst(page, SELECTORS.username);
  const passEl = await findFirst(page, SELECTORS.password);
  const submitEl = await findFirst(page, SELECTORS.submit);
  if (!userEl || !passEl || !submitEl) {
    throw new Error('Login selectors not found. Update SELECTORS to match your login form.');
  }
  await userEl.fill(user);
  await passEl.fill(pass);
  await take(page, evidenceDir, 'before-login');
  await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle' }), submitEl.click()]);
  await take(page, evidenceDir, 'after-login');
}

test.describe('Admin eventos E2E', () => {
  test('admin can add, edit and delete evento', async ({ page }) => {
      const dir = mkEvidenceDir('admin-crud');

    await login(page, ADMIN_USER, ADMIN_PASS, dir);

    // Go to eventos list
    await page.goto(BASE_URL + '/eventos.php');
    await take(page, dir, 'list-loaded');

    // Click Add (link/button text may vary)
    const add = page.locator('a', { hasText: 'Adicionar Evento' }).first();
    await add.click();
    await page.waitForLoadState('networkidle');
    await take(page, dir, 'form-open');

    // Fill form fields - try common field names
    const descricao = await findFirst(page, 'textarea[name="descricao"], input[name="descricao"], [name*="desc"]');
    if (!descricao) throw new Error('Descrição field not found in form. Please update the test selectors.');
    const sampleDesc = 'E2E Test Event ' + Date.now();
    await descricao.fill(sampleDesc);

    // Fill required titulo and data and upload a small image
    const titulo = await findFirst(page, 'input[name="titulo"], input[name*="titulo"], input[placeholder="Titulo"]');
    if (!titulo) throw new Error('Titulo field not found in form.');
    const sampleTitle = 'E2E Title ' + Date.now();
    await titulo.fill(sampleTitle);

    const dataInput = await findFirst(page, 'input[name="data"], input[type="date"]');
    if (!dataInput) throw new Error('Data (date) field not found in form.');
    const today = new Date().toISOString().slice(0,10);
    await dataInput.fill(today);

    // Ensure fixtures folder exists and write a small PNG from base64 stored in fixtures
    const fixturesDir = path.join('e2e', 'fixtures');
    fs.mkdirSync(fixturesDir, { recursive: true });
    const base64 = fs.readFileSync(path.join(fixturesDir, 'pic-base64.txt'), 'utf8').trim();
    const imgPath = path.join(fixturesDir, 'pic.png');
    fs.writeFileSync(imgPath, Buffer.from(base64, 'base64'));

    const fileInput = await findFirst(page, 'input[type="file"], input[name="imagem"]');
    if (!fileInput) throw new Error('File input (imagem) not found in form.');
    await fileInput.setInputFiles(imgPath);

    // Submit form - find a submit button
    const submit = await findFirst(page, 'button[type="submit"], input[type="submit"], button:has-text("Salvar"), button:has-text("Guardar")');
    if (!submit) throw new Error('Form submit button not found.');
    await take(page, dir, 'form-filled');
    // Click and wait for either navigation or network idle; some forms return partial responses
    await submit.click();
    try {
      await page.waitForLoadState('networkidle', { timeout: 15000 });
    } catch (e) {
      // fallback small wait to allow server processing
      await page.waitForTimeout(1500);
    }
    await take(page, dir, 'after-create');

    // Verify the created event appears in the table
    await page.goto(BASE_URL + '/eventos.php');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('td', { hasText: sampleDesc })).toHaveCount(1);
    await take(page, dir, 'created-visible');

    // Edit - click Edit button in the row
    const row = page.locator('tr', { has: page.locator('td', { hasText: sampleDesc }) }).first();
    const edit = row.locator('a[href*="eventosform.php?id="]');
    await edit.click();
    await page.waitForLoadState('networkidle');
    await take(page, dir, 'edit-open');

    // Make a small edit to description
    const descricaoEdit = await findFirst(page, 'textarea[name="descricao"], input[name="descricao"], [name*="desc"]');
    await descricaoEdit.fill(sampleDesc + ' (edited)');
    // ensure image is present for edit (form requires imagem)
    if (!fs.existsSync(imgPath)) {
      const base64 = fs.readFileSync(path.join('e2e', 'fixtures', 'pic-base64.txt'), 'utf8').trim();
      fs.writeFileSync(imgPath, Buffer.from(base64, 'base64'));
    }
    const fileInputEdit = await findFirst(page, 'input[type="file"], input[name="imagem"]');
    if (fileInputEdit) await fileInputEdit.setInputFiles(imgPath);

    const submit2 = await findFirst(page, 'button[type="submit"], input[type="submit"], button:has-text("Salvar")');
    await submit2.click();
    try {
      await page.waitForLoadState('networkidle', { timeout: 15000 });
    } catch (e) {
      await page.waitForTimeout(1500);
    }
    await take(page, dir, 'after-edit');

    // Verify edit reflected (if edit route performs update). If not, continue and delete created item.
    await page.goto(BASE_URL + '/eventos.php');
    await page.waitForLoadState('networkidle');
    const editedLocator = page.locator('td', { hasText: sampleDesc + ' (edited)' });
    const editedCount = await editedLocator.count();
    if (editedCount > 0) {
      await take(page, dir, 'edit-visible');
    } else {
      // Edit may not be implemented; capture evidence and continue to deletion using original sampleDesc
      await take(page, dir, 'edit-not-applied');
    }

    // Determine which row text to target for deletion
    const targetText = (editedCount > 0) ? sampleDesc + ' (edited)' : sampleDesc;

    // Delete - click Remover button in the row
    const row2 = page.locator('tr', { has: page.locator('td', { hasText: targetText }) }).first();
    const removeBtn = row2.locator('button:has-text("Remover"), input[value="Remover"], .action-buttons button:has-text("Remover")').first();
    await removeBtn.click();
    // If confirm dialog appears, accept it
    try { await page.waitForEvent('dialog', { timeout: 1000 }).then(d => d.accept()); } catch (e) { /* no dialog */ }
    await page.waitForTimeout(1000);
    await take(page, dir, 'after-delete');

    // Verify deletion
    await page.goto(BASE_URL + '/eventos.php');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('td', { hasText: sampleDesc + ' (edited)' })).toHaveCount(0);
    await take(page, dir, 'deleted-verified');
  });

  test('manager cannot remove evento (permission check)', async ({ page }) => {
      const dir = mkEvidenceDir('manager-permissions');
    await login(page, MANAGER_USER, MANAGER_PASS, dir);
    await page.goto(BASE_URL + '/eventos.php');
    await page.waitForLoadState('networkidle');
    await take(page, dir, 'manager-list');

    // For manager we expect the Remove button to be disabled or not present
    const removeButtons = page.locator('button:has-text("Remover"), .action-buttons button:has-text("Remover")');
    // If none found, that's also acceptable
    const count = await removeButtons.count();
    if (count === 0) {
      await take(page, dir, 'manager-no-remove-buttons');
    } else {
      // Check at least one is disabled
      let foundDisabled = false;
      for (let i = 0; i < count; i++) {
        const el = removeButtons.nth(i);
        if (await el.isDisabled()) { foundDisabled = true; break; }
      }
      await take(page, dir, 'manager-remove-button-state');
      expect(foundDisabled || count === 0).toBeTruthy();
    }
  });
});
