# 🚀 Laravel Public API

Projeto desenvolvido com **Laravel** com foco educacional para demonstrar conceitos fundamentais utilizados na construção de APIs REST.

# Rodar o projeto

## Requisitos

- PHP ^8.3
- Composer

## Passos

```bash
# 1. Clonar e acessar o projeto
git clone https://github.com/CarlosSTS/laravel_public_api
cd laravel_public_api

# 2. Instalar dependências
composer install

# 3. Configurar variáveis de ambiente
cp .env.example .env

# 4. Gerar a APP_KEY
php artisan key:generate

# 5. Criar arquivo do banco SQLite (se for usar SQLite - padrão)
touch database/database.sqlite

# 6. Rodar as migrations (cria as tabelas)
php artisan migrate

# 7. Popular o banco com dados iniciais (seeders)
php artisan db:seed

# 8. Iniciar o servidor de desenvolvimento
php artisan serve
```

> Edite o `.env` com as credenciais do seu banco e rode `php artisan migrate` e `php artisan db:seed`.

## 📚 Conteúdo

- Migrations
- Models
- Seeders
- Versionamento
- Middlewares
- CORS
- Resources

## 🧭 Fluxo Geral

```text
Request
↓
Route
↓
Middleware
↓
Controller
↓
Model
↓
Resource
↓
JSON Response
```

---

# 🗄️ Migrations

## O que são

Migrations controlam alterações na estrutura do banco.

## Para que servem

- Criar tabelas
- Alterar colunas
- Versionar banco

## Criando

```bash
php artisan make:migration create_products_table
```

### Adiconando um nova coluna em uma tabela existente

```bash
php artisan make:migration add_abilities_to_users_table --table=users
```

Gerado em:

```text
database/migrations/
```

## Executando

```bash
php artisan migrate
```

Comandos úteis:

```bash
php artisan migrate:rollback
php artisan migrate:fresh
php artisan migrate:fresh --seed
```

---

# 🧩 Models

Representam entidades e comunicação com banco.

Criar:

```bash
php artisan make:model Product
```

Gerado em:

```text
app/Models/
```

Convenções:

```text
Product → products
Category → categories
```

---

# 🌱 Seeders

Populam banco com dados.

Criar:

```bash
php artisan make:seeder CategoriesTableSeeder
```

Executar:

```bash
php artisan db:seed
php artisan db:seed --class=CategoriesTableSeeder
```

---

# 🔖 Versionamento

```text
/api/v1/products
/api/v2/products
```

Exemplo:

```php
Route::prefix('v1')->group(function () {
    require base_path('routes/api_v1.php');
});
```

---

# 🧱 Middlewares

Interceptam requisições.

Criar:

```bash
php artisan make:middleware CorrelationIdMiddleware
```

Fluxo:

```text
Request
↓
Middleware
↓
Controller
↓
Response
```

---

# 🌐 CORS

Publicar:

```bash
php artisan config:publish cors
```

Limpar cache:

```bash
php artisan config:clear
```

---

# 📦 Resources

Transformam Models em respostas JSON.

Criar:

```bash
php artisan make:resource ProductResource
```

Exemplo:

```php
return ProductResource::collection(
    Product::all()
);
```

Fluxo:

```text
Controller
↓
Model
↓
Resource
↓
JSON
```
