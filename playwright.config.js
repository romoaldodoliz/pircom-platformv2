const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: 'e2e',
  timeout: 60_000,
  retries: 0,
  use: {
    headless: true,
    viewport: { width: 1280, height: 800 },
    actionTimeout: 0,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure'
  },
  projects: [
    { name: 'chromium', use: { browserName: 'chromium' } }
  ]
});
