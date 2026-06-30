## Como rodar o projeto

**Requisitos:** Docker e Docker Compose instalados.

```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
```

Acesse: http://localhost

**Usuário admin padrão:**
- Email: admin@scheduling.com
- Senha: password
