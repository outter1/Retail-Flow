<<<<<<< HEAD
# Retail Flow

## Sobre o Projeto

O **Retail Flow** é um sistema web desenvolvido com **Laravel** e **Filament Admin Panel**, voltado para o controle de produtos e movimentações de estoque em lojas de conveniência.

O sistema foi pensado para auxiliar o Diretor de Varejo **Roberto Mendes**, localizado em Goiânia - GO, na gestão de produtos de giro rápido, principalmente itens perecíveis que exigem controle de validade, temperatura de armazenamento e estoque mínimo.

A aplicação permite cadastrar usuários, produtos e movimentações de estoque, ajudando a evitar perdas por vencimento, falta de produtos essenciais nas prateleiras e falhas na cadeia de frio.

---

## Objetivo

O objetivo do sistema é fornecer uma solução simples e eficiente para:

* Cadastrar produtos de loja de conveniência;
* Controlar entradas e saídas de estoque;
* Monitorar produtos perecíveis;
* Registrar data de validade;
* Registrar temperatura de armazenamento;
* Identificar produtos abaixo do estoque mínimo;
* Organizar os dados de usuários, produtos e movimentações.

---

## Tecnologias Utilizadas

* PHP
* Laravel
* Filament Admin Panel
* MySQL
* Composer
* Node.js / NPM
* Blade
* Livewire
* HTML, CSS e JavaScript

---

## Estrutura Principal do Sistema

O sistema é composto por três entidades principais:

### Usuário

Representa os usuários responsáveis pelo cadastro e gerenciamento dos produtos.

Campos principais:

* ID do usuário
* Nome
* CPF
* Login
* Ocupação

### Produto

Representa os produtos cadastrados na loja de conveniência.

Campos principais:

* ID do produto
* ID do usuário responsável
* Nome
* Marca
* Cor
* Textura
* Peso
* Unidade de medida
* Aplicação
* Categoria
* Temperatura de armazenamento
* Data de validade
* Estoque mínimo

### Estoque

Representa as movimentações de entrada e saída dos produtos.

Campos principais:

* ID do estoque
* ID do produto
* Quantidade atual
* Quantidade movimentada
* Tipo de movimentação
* Data da movimentação

---

## DER — Diagrama Entidade Relacionamento

O relacionamento entre as tabelas segue a seguinte estrutura:

```text
USUÁRIO 1:N PRODUTO
PRODUTO 1:N ESTOQUE
```

### Explicação

Um usuário pode cadastrar vários produtos.

Um produto pertence a apenas um usuário.

Um produto pode ter várias movimentações de estoque.

Uma movimentação de estoque pertence a apenas um produto.

### Estrutura resumida

```text
USUÁRIO
- id_usuario PK
- nome
- cpf
- login
- ocupacao

        1
        |
        | cadastra
        |
        N

PRODUTO
- id_produto PK
- id_usuario FK
- nome
- marca
- cor
- textura
- peso
- unidade_medida
- aplicacao
- categoria
- temperatura_armazenamento
- data_validade
- estoque_minimo

        1
        |
        | possui movimentações
        |
        N

ESTOQUE
- id_estoque PK
- id_produto FK
- quantidade_atual
- quantidade_movimentacao
- tipo_movimentacao
- data_movimentacao
```

---

## Funcionalidades

### Cadastro de Usuários

O sistema permite cadastrar usuários responsáveis pela operação do sistema.

Funcionalidades:

* Criar usuário;
* Editar usuário;
* Listar usuários;
* Excluir usuários;
* Buscar usuários cadastrados.

---

### Cadastro de Produtos

O sistema permite cadastrar produtos da loja de conveniência.

Funcionalidades:

* Criar produto;
* Editar produto;
* Listar produtos;
* Excluir produtos;
* Buscar produtos cadastrados;
* Registrar categoria do produto;
* Registrar temperatura de armazenamento;
* Registrar data de validade;
* Definir estoque mínimo.

Categorias sugeridas:

* Bebida;
* Salgado;
* Doce.

---

### Gestão de Estoque

O sistema permite registrar movimentações de estoque dos produtos cadastrados.

Funcionalidades:

* Registrar entrada de produto;
* Registrar saída de produto;
* Informar quantidade atual;
* Informar quantidade movimentada;
* Registrar data da movimentação;
* Visualizar histórico de movimentações.

Tipos de movimentação:

* Entrada;
* Saída.

---

## Requisitos Funcionais

### RF-01 — Login no sistema

O sistema deve permitir acesso ao painel administrativo por meio de login.

### RF-02 — Cadastro de usuários

O sistema deve permitir cadastrar usuários com nome, CPF, login e ocupação.

### RF-03 — Listagem de usuários

O sistema deve permitir visualizar todos os usuários cadastrados.

### RF-04 — Cadastro de produtos

O sistema deve permitir cadastrar produtos com informações gerais e específicas.

### RF-05 — Listagem de produtos

O sistema deve permitir visualizar todos os produtos cadastrados.

### RF-06 — Edição de produtos

O sistema deve permitir alterar informações de produtos cadastrados.

### RF-07 — Exclusão de produtos

O sistema deve permitir excluir produtos cadastrados.

### RF-08 — Registro de movimentação de estoque

O sistema deve permitir registrar entradas e saídas de produtos no estoque.

### RF-09 — Controle de validade

O sistema deve permitir registrar a data de validade dos produtos.

### RF-10 — Controle de temperatura

O sistema deve permitir registrar a temperatura ideal de armazenamento dos produtos.

### RF-11 — Controle de estoque mínimo

O sistema deve permitir definir uma quantidade mínima para cada produto.

### RF-12 — Consulta de movimentações

O sistema deve permitir consultar as movimentações de estoque realizadas.

---

## Requisitos Não Funcionais

### RNF-01 — Usabilidade

O sistema deve possuir uma interface simples e organizada.

### RNF-02 — Segurança

O sistema deve restringir o acesso ao painel administrativo apenas para usuários autenticados.

### RNF-03 — Organização dos dados

O sistema deve manter os dados separados em tabelas específicas: usuários, produtos e estoques.

### RNF-04 — Integridade referencial

O sistema deve utilizar chaves primárias e estrangeiras para manter os relacionamentos entre as tabelas.

### RNF-05 — Desempenho

O sistema deve carregar as telas de cadastro e listagem de forma rápida.

### RNF-06 — Manutenibilidade

O código deve seguir a estrutura padrão do Laravel, facilitando futuras alterações.

### RNF-07 — Compatibilidade

O sistema deve funcionar em ambiente local com PHP, Laravel, Composer, MySQL e navegador web.

### RNF-08 — Padronização visual

O sistema deve utilizar o painel Filament para manter uma interface administrativa padronizada.

---

## Casos de Teste

### CT-001 — Cadastro de usuário válido

**Objetivo:** Verificar se o sistema cadastra um usuário corretamente.

**Pré-condição:** Estar logado no painel administrativo.

**Passos:**

1. Acessar o módulo de usuários.
2. Clicar em criar usuário.
3. Preencher nome, CPF, login e ocupação.
4. Salvar.

**Resultado esperado:** O usuário deve ser cadastrado e exibido na listagem.

---

### CT-002 — Cadastro de produto válido

**Objetivo:** Verificar se o sistema cadastra um produto corretamente.

**Pré-condição:** Existir pelo menos um usuário cadastrado.

**Passos:**

1. Acessar o módulo de produtos.
2. Clicar em criar produto.
3. Selecionar o usuário responsável.
4. Preencher os dados do produto.
5. Informar categoria, temperatura, validade e estoque mínimo.
6. Salvar.

**Resultado esperado:** O produto deve ser cadastrado e aparecer na listagem.

---

### CT-003 — Cadastro de produto sem nome

**Objetivo:** Verificar se o sistema impede cadastro de produto sem nome.

**Pré-condição:** Estar na tela de cadastro de produto.

**Passos:**

1. Deixar o campo nome vazio.
2. Preencher os demais campos.
3. Tentar salvar.

**Resultado esperado:** O sistema deve exibir erro informando que o campo nome é obrigatório.

---

### CT-004 — Registro de entrada no estoque

**Objetivo:** Verificar se o sistema registra entrada de produto no estoque.

**Pré-condição:** Existir produto cadastrado.

**Passos:**

1. Acessar o módulo de estoque.
2. Clicar em criar movimentação.
3. Selecionar o produto.
4. Informar quantidade atual.
5. Informar quantidade movimentada.
6. Selecionar tipo de movimentação como Entrada.
7. Informar data da movimentação.
8. Salvar.

**Resultado esperado:** A movimentação deve ser salva como entrada.

---

### CT-005 — Registro de saída no estoque

**Objetivo:** Verificar se o sistema registra saída de produto no estoque.

**Pré-condição:** Existir produto cadastrado.

**Passos:**

1. Acessar o módulo de estoque.
2. Clicar em criar movimentação.
3. Selecionar o produto.
4. Informar quantidade atual.
5. Informar quantidade movimentada.
6. Selecionar tipo de movimentação como Saída.
7. Informar data da movimentação.
8. Salvar.

**Resultado esperado:** A movimentação deve ser salva como saída.

---

### CT-006 — Produto com validade registrada

**Objetivo:** Verificar se o sistema salva corretamente a data de validade do produto.

**Pré-condição:** Estar no cadastro de produto.

**Passos:**

1. Preencher os dados do produto.
2. Informar uma data de validade.
3. Salvar.

**Resultado esperado:** A data de validade deve aparecer corretamente no cadastro do produto.

---

### CT-007 — Produto com temperatura registrada

**Objetivo:** Verificar se o sistema salva corretamente a temperatura de armazenamento.

**Pré-condição:** Estar no cadastro de produto.

**Passos:**

1. Preencher os dados do produto.
2. Informar temperatura de armazenamento.
3. Salvar.

**Resultado esperado:** A temperatura deve ser salva e exibida na listagem ou edição do produto.

---

## Estrutura de Pastas Importantes

```text
app/
├── Models/
│   ├── Usuario.php
│   ├── Produto.php
│   └── Estoque.php
│
├── Filament/
│   └── Resources/
│       ├── UsuarioResource.php
│       ├── ProdutoResource.php
│       └── EstoqueResource.php

database/
└── migrations/
    ├── create_usuarios_table.php
    ├── create_produtos_table.php
    └── create_estoques_table.php

routes/
└── web.php

config/
└── app.php
```

---

## Instalação do Projeto

### 1. Clonar ou copiar o projeto

Coloque a pasta do projeto em seu ambiente local.

### 2. Instalar dependências do PHP

```bash
composer install
```

### 3. Instalar dependências do Node

```bash
npm install
```

### 4. Copiar o arquivo de ambiente

```bash
cp .env.example .env
```

No Windows, se necessário:

```bash
copy .env.example .env
```

### 5. Gerar a chave da aplicação

```bash
php artisan key:generate
```

### 6. Configurar o banco de dados

No arquivo `.env`, configure:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=retail_flow
DB_USERNAME=root
DB_PASSWORD=
```

Ajuste o usuário e senha conforme o seu MySQL.

### 7. Rodar as migrations

```bash
php artisan migrate
```

Caso queira recriar todas as tabelas do zero:

```bash
php artisan migrate:fresh
```

### 8. Criar usuário do Filament

```bash
php artisan make:filament-user
```

Informe nome, e-mail e senha para acessar o painel.

### 9. Rodar o servidor

```bash
php artisan serve
```

Acesse no navegador:

```text
http://127.0.0.1:8000
```

Painel administrativo:

```text
http://127.0.0.1:8000/admin
```

---

## Comandos Úteis

### Limpar cache do Laravel

```bash
php artisan optimize:clear
```

### Limpar cache de views

```bash
php artisan view:clear
```

### Ver rotas do sistema

```bash
php artisan route:list
```

### Recriar banco de dados

```bash
php artisan migrate:fresh
```

### Gerar resources do Filament

```bash
php artisan make:filament-resource Usuario --generate
php artisan make:filament-resource Produto --generate
php artisan make:filament-resource Estoque --generate
```

---

## Rotas Principais

### Rota pública

```text
/
```

Exibe a página inicial padrão do Laravel.

### Painel administrativo

```text
/admin
```

Acesso ao painel Filament.

### Login administrativo

```text
/admin/login
```

Tela de login do Filament.

### Módulo de usuários

```text
/admin/usuarios
```

### Módulo de produtos

```text
/admin/produtos
```

### Módulo de estoque

```text
/admin/estoques
```

---

## Ambiente de Desenvolvimento

Exemplo de ambiente utilizado:

* Sistema Operacional: Windows ou Linux
* Linguagem: PHP
* Framework: Laravel
* Painel Administrativo: Filament
* Banco de Dados: MySQL
* Servidor local: PHP Artisan Serve
* Editor: Visual Studio Code

---

## Observações Importantes

* As migrations definem as colunas reais do banco.
* As models definem quais campos podem ser preenchidos usando `$fillable`.
* Os resources do Filament definem os campos que aparecem no formulário e na tabela.
* Os nomes dos campos devem ser iguais na migration, model e resource.
* Se um campo existir na migration, mas não estiver no `$fillable`, ele pode não ser salvo.
* Se um campo estiver no formulário, mas não existir no banco, o sistema pode apresentar erro.
* Se alterar uma migration já executada, pode ser necessário rodar `php artisan migrate:fresh`.

---

## Autor

Projeto desenvolvido por:

**Gabriel Bastos**

---

## Nome do Sistema

**Retail Flow — Sistema de Controle de Produtos e Estoque para Lojas de Conveniência**
=======
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
>>>>>>> 2a5b6e7 (Projeto)
