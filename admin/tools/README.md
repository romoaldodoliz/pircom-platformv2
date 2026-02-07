Teste automatizado para simular um utilizador `manager` e capturar screenshot

Passos:

1. Instalar dependências (Node.js e npm devem estar instalados):

```bash
cd admin/tools
npm init -y
npm install puppeteer --save-dev
```

2. Executar o teste (gera `manager_test.png` no mesmo diretório):

```bash
node puppeteer_test.js
```

Observações:
- O teste abre a página estática `test_eventos_manager.html` que referencia os assets existentes (`../assets/js/notifications.js`, `../assets/css/notifications.css`).
- Isto valida o comportamento cliente: `window.__isAdmin = false` e o clique no botão com a classe `disabled-delete` deve criar um toast visível.
- Não testa a autenticação da app real (sessões) — apenas o comportamento client-side que mostramos em `eventos.php`.
