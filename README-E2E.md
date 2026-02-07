Playwright E2E tests for Pircom admin

Setup

1. From the project root, install dev dependencies:

```bash
npm install
npx playwright install
```

2. Configure environment variables (optional, defaults are provided in the test):

- `BASE_URL` - base admin URL (default: `http://localhost:8888/Pircom/admin`)
- `ADMIN_USER`, `ADMIN_PASS` - admin credentials (defaults provided below)
- `MANAGER_USER`, `MANAGER_PASS` - manager credentials (defaults provided below)
- `LOGIN_USER_SEL`, `LOGIN_PASS_SEL`, `LOGIN_SUBMIT_SEL` - optional overrides for login selectors

Defaults included in the test:

- Admin: `admin@pircom.org.mz` / `password`
- Manager: `manager@pircom.org.mz` / `password`

Run tests

```bash
npm run test:e2e
```

Outputs

- Screenshots and evidence are saved under `tests/evidence/<scenario>/<timestamp>/`.
- Playwright report can be opened with `npm run test:e2e:report` after a run.

Notes

- The test attempts to find common form/selectors. If your admin login or eventos form use different element names, update the selectors in `tests/admin.spec.js`.
- The tests don't upload images by default — if your form requires a file upload to create an event, adapt the test to set the file input.
