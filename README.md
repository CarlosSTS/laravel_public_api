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

---

## 🔖 Versionamento da API

O versionamento permite evoluir a API sem quebrar integrações existentes.

Exemplo:

```text
/api/v1/products
/api/v2/products
```

Neste projeto são apresentadas duas formas de organizar versões da API.

---

### Forma 1 — Versionamento via `routes/api.php`

Nesta abordagem o arquivo principal de rotas redireciona as requisições para arquivos específicos de cada versão.

Estrutura:

```text
routes/
├── api.php
├── api_v1.php
└── api_v2.php
```

Arquivo principal:

```php
// routes/api.php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require base_path('routes/api_v1.php');
});

Route::prefix('v2')->group(function () {
    require base_path('routes/api_v2.php');
});
```

Controllers organizados por versão:

```bash
php artisan make:controller Api/V1/MainController

php artisan make:controller Api/V2/MainController
```

Estrutura gerada:

```text
app/
└── Http/
    └── Controllers/
        └── Api/
            ├── V1/
            │   └── MainController.php
            └── V2/
                └── MainController.php
```

URLs finais:

```text
GET /api/v1/status
GET /api/v2/status
```

---

### Forma 2 — Versionamento via `bootstrap/app.php`

Outra abordagem consiste em registrar as versões diretamente durante a inicialização da aplicação.

Estrutura:

```text
routes/
├── api_v1.php
└── api_v2.php
```

Arquivo:

```php
// bootstrap/app.php

use Illuminate\Support\Facades\Route;

return Application::configure(
    basePath: dirname(__DIR__)
)
->withRouting(
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',

    then: function (): void {

        Route::middleware('api')
            ->prefix('api/v1')
            ->group(
                base_path('routes/api_v1.php')
            );

        Route::middleware('api')
            ->prefix('api/v2')
            ->group(
                base_path('routes/api_v2.php')
            );
    }
)
->create();
```

URLs finais:

```text
GET /api/v1/products
GET /api/v2/products
```

---

## ✅ Resumo

As duas abordagens produzem o mesmo resultado:

```text
/api/v1/*
/api/v2/*
```

A diferença está apenas em **onde o carregamento das versões é organizado**:

- `routes/api.php` → organização centralizada nas rotas
- `bootstrap/app.php` → organização centralizada na inicialização da aplicação

---

## 🧱 Middlewares

Middlewares permitem interceptar requisições HTTP antes que elas cheguem ao controller e também modificar a resposta antes de retorná-la ao cliente.

São úteis para aplicar comportamentos comuns em toda a aplicação.

Fluxo:

```text
Requisição
↓
Middleware
↓
Controller
↓
Resposta
```

---

## 🛠️ Criando um Middleware

Para criar um middleware execute:

```bash
php artisan make:middleware MIDDLEWARE_NAME
```

Exemplo:

```bash
php artisan make:middleware CorrelationIdMiddleware
```

O arquivo será criado em:

```text
app/Http/Middleware/
```

Estrutura gerada:

```php
public function handle(
    Request $request,
    Closure $next
): Response
{
    return $next($request);
}
```

O método `handle()` recebe a requisição, executa regras intermediárias e define se o fluxo continuará.

---

## 🧾 Exemplo — Correlation ID

Neste projeto foi utilizado um middleware responsável por controlar o identificador da requisição.

Funcionamento:

1. Verifica se o header `X-Correlation-ID` existe
2. Caso não exista, gera um identificador único
3. Adiciona o identificador na requisição
4. Retorna o mesmo identificador na resposta

Exemplo:

```php
class CorrelationIdMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $correlationId =
            $request->header(
                'X-Correlation-ID'
            )
            ?: Str::uuid()->toString();

        $request
            ->headers
            ->set(
                'X-Correlation-ID',
                $correlationId
            );

        $response = $next($request);

        $response
            ->headers
            ->set(
                'X-Correlation-ID',
                $correlationId
            );

        return $response;
    }
}
```

Resultado esperado:

```text
Request:
X-Correlation-ID: 123

↓

Response:
X-Correlation-ID: 123
```

Caso o header não seja enviado:

```text
Request:
(sem X-Correlation-ID)

↓

Response:
X-Correlation-ID: generated-uuid
```

---

## 🔗 Registrando o Middleware

No Laravel moderno o middleware pode ser registrado no arquivo:

```text
bootstrap/app.php
```

Exemplo:

```php
->withMiddleware(
    function (
        Middleware $middleware
    ): void {

        $middleware->api(
            prepend: [
                CorrelationIdMiddleware::class
            ]
        );

    }
)
```

Neste caso o middleware será executado para todas as rotas da API.

---

## ▶️ Testando

Exemplo de requisição:

```http
GET /api/status
X-Correlation-ID: abc-123
```

Exemplo de resposta:

```http
HTTP/1.1 200 OK
X-Correlation-ID: abc-123
```

Dessa forma cada requisição pode ser identificada e rastreada durante todo o fluxo da aplicação.

---

## 🌐 CORS (Cross-Origin Resource Sharing)

CORS controla quais origens externas podem acessar a API a partir do navegador.

Esse mecanismo existe para permitir (ou bloquear) requisições entre domínios diferentes.

Exemplo:

```text
Frontend:
http://localhost:3000

↓

API:
http://localhost:8000
```

Sem configuração adequada de CORS, o navegador pode bloquear a comunicação entre aplicações.

---

## ⚙️ Publicando a Configuração

Para gerar o arquivo de configuração execute:

```bash
php artisan config:publish cors
```

O arquivo será disponibilizado em:

```text
config/cors.php
```

Para mudanças nos arquivos de config/cors execute:

```bash
php artisan config:clear
```

Isso irá limpar qualquer cache que exista na aplicação

---
