const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer');

(async () => {
  const filePath = path.resolve(__dirname, 'test_eventos_manager.html');
  const url = 'file://' + filePath;
  const out = path.resolve(__dirname, 'manager_test.png');

  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.setViewport({ width: 1000, height: 800 });

  console.log('Abrindo', url);
  await page.goto(url, { waitUntil: 'networkidle2' });

  // esperar pelo container de notificações ou pelo toast criado
  try {
    await page.waitForSelector('#notification-container .notification', { timeout: 4000 });
  } catch (e) {
    console.warn('Toast não apareceu dentro do timeout, capturando estado da página mesmo assim.');
  }

  // dar um pequeno atraso para o toast ficar visível
  await page.waitForTimeout(800);

  await page.screenshot({ path: out, fullPage: false });
  console.log('Screenshot salvo em', out);

  await browser.close();
})();
