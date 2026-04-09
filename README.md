# DoraNina

Loja de bolos em PHP com catalogo, carrinho, login, pedidos e painel admin.

## Rodando localmente

1. Coloque a pasta em um servidor PHP local, como o XAMPP.
2. Para PostgreSQL/Neon, importe `database/neon_postgres.sql` e configure `DATABASE_URL`.
3. Para MySQL local, importe `database/loja_bolos.sql` e configure as variaveis `DB_*` se necessario.
4. Abra `http://localhost/doranina`.

## Variaveis de ambiente

Copie os valores de `.env.example` para o ambiente local ou para a Vercel:

- `APP_SECRET`
- `DATABASE_URL`
- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `SMTP_HOST`
- `SMTP_PORT`
- `SMTP_USER`
- `SMTP_PASS`
- `SMTP_FROM_EMAIL`
- `SMTP_FROM_NAME`
- `EMAIL_PEDIDOS_DESTINO`
- `EMAIL_PEDIDOS_NOME`

## Deploy na Vercel

O projeto usa um roteador unico em `api/index.php` com `vercel-php@0.9.0`.

Antes de publicar em producao, configure:

1. `DATABASE_URL` apontando para o Postgres de producao.
2. `APP_SECRET` com um valor longo e aleatorio.
3. As variaveis SMTP, se quiser receber os emails de pedido.

Sem banco de producao, a home sobe, mas login, painel e pedidos ficam indisponiveis.
