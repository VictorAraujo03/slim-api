# Slim API

API REST de exemplo construída com [Slim Framework 4](https://www.slimframework.com/), [PHP-DI](https://php-di.org/) e [Eloquent ORM](https://laravel.com/docs/eloquent) (via `illuminate/database`), com autenticação por token JWT.

## Stack

- PHP 8+
- Slim Framework 4
- PHP-DI (injeção de dependência)
- Illuminate Database (Eloquent) como ORM
- Firebase PHP-JWT
- vlucas/phpdotenv (variáveis de ambiente)

## Requisitos

- PHP 8.0+
- Composer
- MySQL (ou compatível)

## Instalação

```bash
composer install
```

Copie o arquivo de exemplo de variáveis de ambiente e ajuste com suas credenciais locais:

```bash
cp .env.example .env
```

Edite o `.env`:

```
DB_DRIVER=mysql
DB_HOST=localhost
DB_DATABASE=slim
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8
DB_COLLATION=utf8_unicode_ci

JWT_SECRET_KEY=troque-por-uma-string-aleatoria-longa
```

> `JWT_SECRET_KEY` é usada para assinar/validar os tokens de autenticação. Nunca reaproveite o valor de exemplo em produção.

Crie o banco de dados (`slim`, ou o nome que você definiu em `DB_DATABASE`) e rode o script de seed para criar a tabela `produtos` com alguns registros de exemplo:

```bash
php db.php
```

## Executando

```bash
composer start
```

Isso sobe o servidor embutido do PHP em `http://localhost:8080`, com `public/` como document root.

Se preferir usar Apache/XAMPP, aponte o VirtualHost (ou acesse via `http://localhost/slim-api/public`) — o `basePath` da aplicação é detectado automaticamente a partir do `SCRIPT_NAME`, então funciona tanto na raiz quanto em subpastas.

## Autenticação

As rotas sob `/api/v1/*` exigem um token JWT enviado no header `X-Token`. O token é obtido via login:

```
POST /api/token
Content-Type: application/json

{
  "email": "usuario@exemplo.com",
  "senha": "sua-senha"
}
```

Resposta:

```json
{ "chave": "eyJ0eXAiOi..." }
```

Use esse valor no header das próximas requisições:

```
X-Token: eyJ0eXAiOi...
```

## Rotas

| Método | Rota                              | Descrição                    | Requer token |
|--------|-----------------------------------|-------------------------------|--------------|
| POST   | `/api/token`                      | Login / geração de token JWT  | Não          |
| GET    | `/api/v1/produtos/lista`          | Lista todos os produtos       | Sim          |
| GET    | `/api/v1/produtos/lista/{id}`     | Busca um produto por id       | Sim          |
| POST   | `/api/v1/produtos/adiciona`       | Cria um produto               | Sim          |
| PUT    | `/api/v1/produtos/atualiza/{id}`  | Atualiza um produto           | Sim          |
| DELETE | `/api/v1/produtos/deleta/{id}`    | Remove um produto             | Sim          |

Todas as requisições com corpo (`POST`/`PUT`) devem enviar `Content-Type: application/json` com um JSON no body.

## Estrutura

```
app/
  dependencies.php   # definições do container (DB, logger)
  middleware.php      # middlewares globais (JWT, CORS)
  repositories.php
  routes.php           # agrega as rotas
  routes/
    autenticacao.php   # POST /api/token
    produtos.php        # CRUD de produtos
  settings.php          # configurações (lidas do .env)
  Models/
    Produto.php
    Usuario.php
src/                    # skeleton padrão do Slim (handlers, actions, settings)
public/
  index.php             # front controller
db.php                  # script de seed/migração da tabela produtos
```

## Testes

```bash
composer test
```
