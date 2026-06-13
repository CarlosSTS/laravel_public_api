## 🗄️ Criando Migrations

As migrations são utilizadas para versionar e controlar alterações na estrutura do banco de dados. Elas permitem que o esquema seja reproduzido e compartilhado entre diferentes ambientes.

Para criar uma nova migration, execute:

```bash
php artisan make:migration MIGRATION_NAME
```

Substitua `MIGRATION_NAME` por um nome que descreva a alteração.

Exemplo:

```bash
php artisan make:migration create_products_table
```

O arquivo será criado em:

```text
database/migrations/
```

Dentro da migration são definidas as tabelas, colunas, índices e relacionamentos.

---

## ▶️ Executando as Migrations

Após criar ou editar uma migration, execute:

```bash
php artisan migrate
```

Esse comando aplica todas as migrations pendentes ao banco de dados.

O histórico das migrations executadas é armazenado automaticamente na tabela:

```text
migrations
```

Assim o Laravel consegue identificar quais alterações já foram aplicadas.

---

## 🧩 Criando Models

Os Models representam as entidades da aplicação e realizam a comunicação com o banco de dados através do Eloquent ORM.

Cada model normalmente representa uma tabela.

Para criar um model:

```bash
php artisan make:model MODEL_NAME
```

Substitua `MODEL_NAME` pelo nome da entidade.

Exemplo:

```bash
php artisan make:model Product
```

O arquivo será criado em:

```text
app/Models/
```

### Convenções do Laravel

- O model utiliza nome no **singular**
- Utiliza **PascalCase**
- O Laravel associa automaticamente o model à tabela no **plural**

Exemplo:

```text
Model: Product
Tabela: products
```

```text
Model: Category
Tabela: categories
```

---

## 🌱 Seeders

Seeders são utilizados para popular o banco de dados com dados iniciais ou dados para testes.

Exemplos comuns:

- Categorias padrão
- Usuário administrador
- Produtos iniciais
- Dados de desenvolvimento

### Criando um Seeder

Execute:

```bash
php artisan make:seeder SEEDER_NAME
```

Exemplo:

```bash
php artisan make:seeder CategoriesTableSeeder
```

O arquivo será criado em:

```text
database/seeders/
```

Dentro do método `run()` defina os registros que serão inseridos.

Exemplo:

```php
public function run()
{
    Category::create([
        'name' => 'Eletrônicos'
    ]);
}
```

---

## ▶️ Executando Seeders

Para executar um seeder específico:

```bash
php artisan db:seed --class=CategoriesTableSeeder
```

Para executar todos os seeders registrados em:

```text
DatabaseSeeder.php
```

Execute:

```bash
php artisan db:seed
```

Também é possível recriar o banco e executar as seeds automaticamente:

```bash
php artisan migrate:fresh --seed
```
