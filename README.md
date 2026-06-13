## 🗄️ Criando Migrations

As migrations são utilizadas para versionar e controlar alterações na estrutura do banco de dados, permitindo que o esquema seja reproduzido e compartilhado entre ambientes.

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

Dentro da migration serão definidas as colunas, índices e relacionamentos da tabela.

---

## ▶️ Executando as Migrations

Após criar ou editar uma migration, execute:

```bash
php artisan migrate
```

Esse comando aplica todas as migrations pendentes ao banco de dados.

O histórico das migrations executadas é armazenado na tabela:

```text
migrations
```

Dessa forma, o Laravel consegue identificar quais alterações já foram aplicadas.

---

## 🧩 Criando Models

Os Models representam as entidades da aplicação e são responsáveis pela comunicação com o banco de dados utilizando o Eloquent ORM.

Cada model normalmente está associado a uma tabela.

Para criar um model, execute:

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

Por convenção:

- O nome do model é escrito no **singular**
- Utiliza **PascalCase**
- O Laravel associa automaticamente o model à tabela no **plural**

Exemplo:

```text
Model: Product
Tabela: products
```

Outro exemplo:

```text
Model: Category
Tabela: categories
```
