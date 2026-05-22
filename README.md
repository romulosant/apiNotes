# Projeto 01 — Notes API

API REST de anotações com autenticação JWT, desenvolvida com Laravel.

O projeto permite que usuários criem uma conta, façam login, recebam um token de acesso e gerenciem suas próprias notas.

---

## Objetivo

Criar uma API simples de anotações com autenticação.

Esse projeto serve para estudar a base de uma API Laravel, incluindo:

* Rotas de API
* Controllers
* Models
* Migrations
* Relacionamentos
* Autenticação via token
* CRUD
* Validação
* API Resources
* Respostas JSON
* Status codes

---

## Tecnologias usadas

* Laravel
* SQLite
* Eloquent ORM
* JWT Auth / Guard `api`
* Form Requests
* API Resources
* Postman ou Insomnia

---

## Entidade principal

### `notes`

Campos da tabela:

| Campo        | Descrição                  |
| ------------ | -------------------------- |
| `id`         | ID da nota                 |
| `user_id`    | ID do usuário dono da nota |
| `title`      | Título da nota             |
| `content`    | Conteúdo da nota           |
| `created_at` | Data de criação            |
| `updated_at` | Data de atualização        |

---

## Relacionamentos

Um usuário pode ter várias notas:

```php
public function notes()
{
    return $this->hasMany(Note::class);
}
```

Uma nota pertence a um usuário:

```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

---

## Rotas da API

### Rotas públicas

| Método | Rota            | Descrição         |
| ------ | --------------- | ----------------- |
| `POST` | `/api/register` | Registrar usuário |
| `POST` | `/api/login`    | Fazer login       |

### Rotas autenticadas

Todas as rotas abaixo precisam do token JWT no header.

| Método   | Rota                   | Descrição                   |
| -------- | ---------------------- | --------------------------- |
| `POST`   | `/api/v1/logout`       | Fazer logout                |
| `GET`    | `/api/v1/me`           | Ver usuário autenticado     |
| `GET`    | `/api/v1/notes`        | Listar notas                |
| `POST`   | `/api/v1/notes`        | Criar nota                  |
| `GET`    | `/api/v1/notes/{note}` | Exibir nota                 |
| `PUT`    | `/api/v1/notes/{note}` | Atualizar nota              |
| `PATCH`  | `/api/v1/notes/{note}` | Atualizar nota parcialmente |
| `DELETE` | `/api/v1/notes/{note}` | Deletar nota                |

---

## Autenticação

Após fazer login, a API retorna um token JWT.

Esse token deve ser enviado nas rotas protegidas usando o header:

```http
Authorization: Bearer SEU_TOKEN_AQUI
Accept: application/json
```

---

## Endpoints de autenticação

### Registrar usuário

```http
POST /api/register
```

Body:

```json
{
  "name": "Rômulo",
  "email": "user@email.com",
  "password": "12345678"
}
```

Resposta `200`:

```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "Rômulo",
    "email": "user@email.com"
  }
}
```

Validações:

| Campo      | Regra                                       |
| ---------- | ------------------------------------------- |
| `name`     | Obrigatório, string                         |
| `email`    | Obrigatório, email, único na tabela `users` |
| `password` | Obrigatório, string, mínimo de 8 caracteres |

---

### Login

```http
POST /api/login
```

Body:

```json
{
  "email": "user@email.com",
  "password": "12345678"
}
```

Resposta `200`:

```json
{
  "access_token": "jwt_token_here",
  "token_type": "bearer"
}
```

Resposta `401`:

```json
{
  "error": "Unauthorized"
}
```

---

### Ver usuário autenticado

```http
GET /api/v1/me
```

Headers:

```http
Authorization: Bearer SEU_TOKEN_AQUI
Accept: application/json
```

Resposta `200`:

```json
{
  "user": {
    "id": 1,
    "name": "Rômulo",
    "email": "user@email.com"
  }
}
```

---

### Logout

```http
POST /api/v1/logout
```

Headers:

```http
Authorization: Bearer SEU_TOKEN_AQUI
Accept: application/json
```

Resposta `200`:

```json
{
  "message": "Logged out successfully"
}
```

---

## Endpoints de notas

Todas as rotas de notas exigem autenticação.

---

### Listar notas

```http
GET /api/v1/notes
```

Retorna as notas do usuário autenticado, ordenadas da mais recente para a mais antiga, com paginação de 10 itens por página.

Resposta `200`:

```json
{
  "data": [
    {
      "id": 1,
      "title": "Minha nota",
      "content": "Conteúdo da nota",
      "created_at": "2026-05-21 10:00:00",
      "updated_at": "2026-05-21 10:00:00"
    }
  ]
}
```

Como a listagem usa paginação, a resposta também pode conter dados de paginação, como links e informações de página, dependendo da estrutura retornada pelo Laravel Resource Collection.

---

### Criar nota

```http
POST /api/v1/notes
```

Body:

```json
{
  "title": "Minha nota",
  "content": "Texto da nota"
}
```

Resposta `201`:

```json
{
  "data": {
    "id": 1,
    "title": "Minha nota",
    "content": "Texto da nota",
    "created_at": "2026-05-21 10:00:00",
    "updated_at": "2026-05-21 10:00:00"
  }
}
```

---

### Exibir uma nota

```http
GET /api/v1/notes/{note}
```

Resposta `200`:

```json
{
  "data": {
    "id": 1,
    "title": "Minha nota",
    "content": "Conteúdo",
    "created_at": "2026-05-21 10:00:00",
    "updated_at": "2026-05-21 10:00:00"
  }
}
```

Caso a nota não pertença ao usuário autenticado:

```json
{
  "message": "Acesso negado."
}
```

Status: `403`

---

### Atualizar nota

```http
PUT /api/v1/notes/{note}
```

Também é possível usar:

```http
PATCH /api/v1/notes/{note}
```

Body:

```json
{
  "title": "Novo título",
  "content": "Novo conteúdo"
}
```

Resposta `200`:

```json
{
  "data": {
    "id": 1,
    "title": "Novo título",
    "content": "Novo conteúdo",
    "created_at": "2026-05-21 10:00:00",
    "updated_at": "2026-05-21 10:05:00"
  }
}
```

Caso a nota não pertença ao usuário autenticado:

```json
{
  "message": "Acesso negado."
}
```

Status: `403`

---

### Deletar nota

```http
DELETE /api/v1/notes/{note}
```

Resposta `200`:

```json
{
  "message": "Nota removida com sucesso!"
}
```

Caso a nota não pertença ao usuário autenticado:

```json
{
  "message": "Acesso negado."
}
```

Status: `403`

---

## Regras de negócio

* Apenas usuários autenticados podem acessar as rotas `/api/v1`.
* Cada usuário só pode listar suas próprias notas.
* Cada usuário só pode visualizar, editar e deletar notas que pertencem a ele.
* As notas são listadas da mais recente para a mais antiga.
* A listagem de notas usa paginação com 10 itens por página.
* O login retorna um token JWT.
* O logout invalida o token atual.

---

## Status codes

| Código | Significado                                      |
| ------ | ------------------------------------------------ |
| `200`  | Requisição realizada com sucesso                 |
| `201`  | Recurso criado com sucesso                       |
| `401`  | Usuário não autenticado ou credenciais inválidas |
| `403`  | Usuário sem permissão para acessar o recurso     |
| `404`  | Recurso não encontrado                           |
| `422`  | Erro de validação                                |
| `500`  | Erro interno do servidor                         |

---

## Como instalar o projeto

Clone o repositório:

```bash
git clone URL_DO_REPOSITORIO
```

Entre na pasta do projeto:

```bash
cd notes-api
```

Instale as dependências:

```bash
composer install
```

Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

Configure o banco de dados SQLite no arquivo `.env`:

```env
DB_CONNECTION=sqlite
```

Crie o arquivo do banco SQLite:

```bash
touch database/database.sqlite
```

Execute as migrations:

```bash
php artisan migrate
```

Se o projeto usa JWT Auth, gere a chave JWT:

```bash
php artisan jwt:secret
```

Inicie o servidor:

```bash
php artisan serve
```

A API ficará disponível em:

```txt
http://127.0.0.1:8000
```

---

## Exemplo de fluxo no Postman ou Insomnia

1. Criar usuário:

```http
POST /api/register
```

2. Fazer login:

```http
POST /api/login
```

3. Copiar o `access_token` retornado.

4. Usar o token nas rotas protegidas:

```http
Authorization: Bearer SEU_TOKEN_AQUI
```

5. Criar uma nota:

```http
POST /api/v1/notes
```

6. Listar as notas:

```http
GET /api/v1/notes
```

7. Editar uma nota:

```http
PUT /api/v1/notes/{note}
```

8. Deletar uma nota:

```http
DELETE /api/v1/notes/{note}
```

---

## Estrutura esperada

```txt
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       └── NoteController.php
│   ├── Requests/
│   │   ├── StoreNoteRequest.php
│   │   └── UpdateNoteRequest.php
│   └── Resources/
│       └── NoteResource.php
├── Models/
│   ├── User.php
│   └── Note.php

database/
└── migrations/

routes/
└── api.php
```

---

## Autor

Desenvolvido por Rômulo Santos

---

## Licença

Este projeto é livre para estudos.
