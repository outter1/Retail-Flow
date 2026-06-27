# GUIA COMPLETO: Construindo um Sistema de Estoque com Laravel 13 e Filament Admin Panel

## Sumário
1. [Arquitetura Geral](#arquitetura-geral)
2. [Etapa 1: Criar as Migrations (Banco de Dados)](#etapa-1-criar-as-migrations)
3. [Etapa 2: Criar os Models (Entidades)](#etapa-2-criar-os-models)
4. [Etapa 3: Criar Filament Resources](#etapa-3-criar-filament-resources)
5. [Erros Comuns e Como Evitar](#erros-comuns)
6. [Fluxo Completo de Dados](#fluxo-completo)

---

## ARQUITETURA GERAL

O projeto funciona assim:

```
USUÁRIO (Filament UI)
        ↓
FILAMENT FORM (EstoqueForm.php)
        ↓ Envia dados
CONTROLLER DO FILAMENT
        ↓ Valida e processa
MODEL (Estoque.php)
        ↓ Mass Assignment ($fillable)
DATABASE (tabela estoques)
```

Cada parte é independente e deve estar SINCRONIZADA:

- **Formulário (Schema)** → Define quais campos aparecem
- **Model ($fillable)** → Define quais campos podem ser salvos em massa
- **Migration (Table)** → Define as colunas reais no banco
- **Table (Columns)** → Define quais colunas aparecem na lista

---

## ETAPA 1: CRIAR AS MIGRATIONS (BANCO DE DADOS)

### O que é uma Migration?
Uma migration é um arquivo PHP que descreve a estrutura das tabelas do banco de dados. É como um "plano de construção" das tabelas.

### Como Criar:
```bash
php artisan make:migration create_usuarios_table
php artisan make:migration create_produtos_table
php artisan make:migration create_estoques_table
```

### MIGRATION 1: Usuarios (Usuários/Operadores)

**Arquivo:** `database/migrations/2026_06_23_024108_create_usuarios_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Esta função é executada quando você faz "php artisan migrate"
     * Ela cria a tabela "usuarios" no banco de dados
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            /**
             * $table->id('id_usuario')
             * 
             * Cria a coluna "id_usuario" como PRIMARY KEY (chave primária)
             * PRIMARY KEY = identificador único de cada usuário
             * AUTO INCREMENT = aumenta automaticamente (1, 2, 3, ...)
             * 
             * Por padrão, Laravel usa "id", mas usamos "id_usuario" por clareza
             */
            $table->id('id_usuario');

            /**
             * $table->string('nome')
             * 
             * Cria coluna "nome" que armazena textos de até 255 caracteres
             * string() = texto curto (até 255 caracteres)
             * text() = texto longo (ilimitado)
             * integer() = número inteiro
             * date() = data (YYYY-MM-DD)
             * timestamp() = data com hora (YYYY-MM-DD HH:MM:SS)
             */
            $table->string('nome');

            /**
             * $table->string('login')
             * 
             * Nome de usuário para login (ex: "gabriel123")
             * Deve ser único para evitar duplicatas
             */
            $table->string('login');

            /**
             * $table->string('tipo_usuario')
             * 
             * Tipo de usuário: "admin", "operador", "gerente", etc
             * Pode ser usado para controle de permissões
             */
            $table->string('tipo_usuario');

            /**
             * $table->timestamps()
             * 
             * Cria automaticamente DUAS colunas:
             * - created_at: quando o registro foi criado
             * - updated_at: quando foi atualizado pela última vez
             * 
             * Útil para auditar e ordenar registros
             */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Esta função é executada quando você faz "php artisan migrate:rollback"
     * Ela DESFAZ a migration (deleta a tabela)
     * Útil quando comete erros e precisa voltar atrás
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
```

### MIGRATION 2: Produtos (Produtos/Itens)

**Arquivo:** `database/migrations/2026_06_23_024118_create_produtos_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            /**
             * $table->id('id_produto')
             * 
             * Chave primária da tabela produtos
             * Cada produto tem um ID único
             */
            $table->id('id_produto');

            /**
             * $table->foreignId('id_usuario')
             * 
             * FOREIGN KEY = chave estrangeira
             * Conecta este produto a um usuário específico
             * 
             * Exemplo:
             * - Produto "Coca-Cola" pertence ao usuário ID 2 (Gabriel)
             * - Produto "Salgado" também pertence ao usuário ID 2
             * - Produto "Doce" pertence ao usuário ID 3 (Roberto)
             * 
             * Relacionamento: MUITOS-PARA-UM (N:1)
             * Muitos produtos → Um usuário
             */
            $table->foreignId('id_usuario');

            // Informações básicas do produto
            $table->string('nome');              // ex: "Coca-Cola"
            $table->string('marca');             // ex: "Coca-Cola Company"
            $table->string('cor');               // ex: "vermelha"
            $table->string('textura');           // ex: "lisa"
            
            // Informações físicas
            $table->decimal('peso', 8, 2);      // ex: 350.50 (peso em gramas/kg)
            $table->string('unidade_medida');   // ex: "gramas", "litros"
            
            // Informações logísticas
            $table->string('aplicacao');         // ex: "bebida"
            $table->string('categoria');         // ex: "refrigerante"
            $table->string('temperatura_armazenamento'); // ex: "-18C"
            $table->date('data_validade');       // ex: "2027-12-31"
            $table->integer('estoque_minimo');   // ex: 100 (quantidade mínima em estoque)

            // Registros de data
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
```

### MIGRATION 3: Estoques (Movimentação de Estoque)

**Arquivo:** `database/migrations/2026_06_23_024126_create_estoques_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estoques', function (Blueprint $table) {
            /**
             * $table->id('id_estoque')
             * 
             * Chave primária: identifica cada movimentação de estoque
             * Cada vez que você registra entrada/saída, cria um novo ID
             */
            $table->id('id_estoque');

            /**
             * $table->foreignId('id_produto')
             *     ->constrained('produtos','id_produto')
             *     ->cascadeOnDelete()
             * 
             * FOREIGN KEY para conectar com a tabela "produtos"
             * 
             * ->constrained('produtos','id_produto')
             * = Link com a tabela "produtos" coluna "id_produto"
             * 
             * ->cascadeOnDelete()
             * = Se deletar um produto, deleta TODAS suas movimentações também
             * Exemplo: Se Coca-Cola for deletada, todas as movimentações dela são deletadas
             * 
             * Sem isso, você teria movimentações órfãs (sem produto pai)
             */
            $table->foreignId('id_produto')
                ->constrained('produtos', 'id_produto')
                ->cascadeOnDelete();

            /**
             * $table->integer('estoque_atual')
             * 
             * Quantidade atual do produto após a movimentação
             * Exemplo: estava com 100, entrou 50 → agora tem 150
             */
            $table->integer('estoque_atual');

            /**
             * $table->string('movimentacao')
             * 
             * Tipo de movimento: "entrada" ou "saida"
             * - "entrada" = quantidade aumenta
             * - "saida" = quantidade diminui
             */
            $table->string('movimentacao');

            /**
             * $table->date('data_movimentacao')
             * 
             * Quando foi a movimentação (ex: 2027-02-05)
             */
            $table->date('data_movimentacao');

            // Registra automaticamente quando foi criado e atualizado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estoques');
    }
};
```

### Como Executar as Migrations:

```bash
# Executa todas as migrations pendentes
php artisan migrate

# Se cometer erro, volta atrás (deleta tudo)
php artisan migrate:rollback

# Volta tudo e executa novamente
php artisan migrate:refresh
```

---

## ETAPA 2: CRIAR OS MODELS (ENTIDADES)

### O que é um Model?
Um Model é uma classe PHP que representa uma tabela do banco de dados. Ele faz a ponte entre sua aplicação e a tabela.

### Como Criar:
```bash
php artisan make:model Usuario
php artisan make:model Produto
php artisan make:model Estoque
```

### MODEL 1: Usuario

**Arquivo:** `app/Models/Usuario.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Usuario
 * 
 * Representa a tabela "usuarios" do banco de dados
 * Toda vez que você quer fazer algo com usuários, usa este Model
 * 
 * Exemplo de uso:
 * $usuario = Usuario::find(1);              // Pega usuário com ID 1
 * $todos = Usuario::all();                  // Pega todos os usuários
 * $novo = Usuario::create([                 // Cria novo usuário
 *     'nome' => 'João',
 *     'login' => 'joao123',
 *     'tipo_usuario' => 'operador'
 * ]);
 */
class Usuario extends Model
{
    /**
     * protected $table = 'usuarios'
     * 
     * Define qual tabela este Model representa
     * Se não especificar, Laravel assume "usuaios" (plural em inglês)
     * Como queremos "usuarios", precisamos especificar
     */
    protected $table = 'usuarios';

    /**
     * protected $primaryKey = 'id_usuario'
     * 
     * Define qual coluna é a chave primária (ID único)
     * Por padrão, Laravel procura por "id"
     * Mas nossa tabela usa "id_usuario", então especificamos
     */
    protected $primaryKey = 'id_usuario';

    /**
     * protected $fillable = [...]
     * 
     * Lista de colunas que podem ser preenchidas em massa
     * 
     * O QUE SIGNIFICA "PREENCHIMENTO EM MASSA"?
     * É quando você faz: Usuario::create(['nome' => 'João', 'login' => 'joao'])
     * 
     * Por segurança, Laravel BLOQUEIA por padrão para evitar ataques
     * Você DEVE listar explicitamente quais campos podem ser editados
     * 
     * Exemplo perigoso (sem $fillable):
     * Se alguém enviar POST /usuarios com dados de admin ou senha,
     * poderia explorar a aplicação
     * 
     * Com $fillable, apenas estes campos são aceitos, outros são ignorados
     */
    protected $fillable = [
        'nome',           // Nome do usuário
        'login',          // Login para autenticação
        'tipo_usuario',   // Tipo: admin, operador, gerente, etc
    ];

    /**
     * Relacionamento: Um usuário tem MUITOS produtos
     * 
     * CARDINALIDADE: 1:N (Um para Muitos)
     * Um usuário pode ter vários produtos
     * 
     * Cada produto tem uma coluna "id_usuario" que aponta para este usuário
     * 
     * Exemplo:
     * - Usuário 2 (Gabriel) tem 3 produtos: Coca-Cola, Salgado, Doce
     * - Usuário 3 (Roberto) tem 2 produtos: Cerveja, Vinho
     * 
     * Como usar:
     * $usuario = Usuario::find(2);
     * $produtos = $usuario->produtos;  // Pega todos os produtos de Gabriel
     * 
     * return $this->hasMany(Produto::class, 'id_usuario');
     * 
     * Explicação:
     * - hasMany() = este usuário tem muitos produtos
     * - Produto::class = qual tabela relacionada (produtos)
     * - 'id_usuario' = qual coluna em "produtos" aponta para "usuarios"
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relacionamento: Um usuário fez MUITAS movimentações de estoque
     * 
     * Um usuário pode registrar várias movimentações
     * Exemplo: Gabriel registrou entrada, saída, entrada, saída...
     */
    public function estoques()
    {
        return $this->hasMany(Estoque::class, 'id_usuario', 'id_usuario');
    }
}
```

### MODEL 2: Produto

**Arquivo:** `app/Models/Produto.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';

    /**
     * $fillable = [...]
     * 
     * Lista TODOS os campos que podem ser preenchidos
     * 
     * IMPORTANTE: Se esquecer de adicionar um campo aqui,
     * e tentar fazer Produto::create(['novo_campo' => 'valor']),
     * Laravel vai IGNORAR o novo_campo silenciosamente
     * 
     * Por isso é comum erro: "Campo não foi salvo no banco"
     * Resposta: "Você adicionou na $fillable?"
     */
    protected $fillable = [
        'id_usuario',
        'nome',
        'marca',
        'cor',
        'textura',
        'peso',
        'unidade_medida',
        'aplicacao',
        'categoria',
        'temperatura_armazenamento',
        'data_validade',
        'estoque_minimo',
    ];

    /**
     * Relacionamento: Um produto pertence a UM usuário
     * 
     * CARDINALIDADE: N:1 (Muitos para Um)
     * Muitos produtos → Um usuário
     * 
     * Cada produto tem uma coluna "id_usuario" que aponta para seu usuário dono
     * 
     * Exemplo:
     * - Produto 1 (Coca-Cola) pertence ao usuário 2 (Gabriel)
     * - Produto 2 (Salgado) pertence ao usuário 2 (Gabriel)
     * - Produto 3 (Doce) pertence ao usuário 3 (Roberto)
     * 
     * Como usar:
     * $produto = Produto::find(1);
     * $dono = $produto->usuario;  // Pega Gabriel
     * 
     * belongsTo() = este produto pertence a um usuário
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relacionamento: Um produto tem MUITAS movimentações
     * 
     * Cada movimentação (entrada/saída) está relacionada a um produto
     * 
     * Exemplo:
     * - Produto "Coca-Cola" tem 10 movimentações (entrou 50, saiu 10, entrou 20, etc)
     * - Produto "Salgado" tem 3 movimentações
     */
    public function estoques()
    {
        return $this->hasMany(Estoque::class, 'id_produto', 'id_produto');
    }
}
```

### MODEL 3: Estoque

**Arquivo:** `app/Models/Estoque.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    protected $table = 'estoques';
    protected $primaryKey = 'id_estoque';

    /**
     * $fillable = [...]
     * 
     * MUITO IMPORTANTE: Adicione APENAS os campos que existem na tabela!
     * 
     * Se adicionar 'id_usuario' mas a tabela não tem essa coluna,
     * vai gerar erro: "Unknown column 'id_usuario' in 'field list'"
     * 
     * Verifique sempre:
     * 1. Campo está na migration? (database/migrations/...)
     * 2. Migration foi executada? (php artisan migrate)
     * 3. Coluna está na $fillable?
     * 
     * Se falta algum, sincronize!
     */
    protected $fillable = [
        'id_produto',        // Qual produto está sendo movimentado
        'estoque_atual',     // Quantidade após a movimentação
        'movimentacao',      // "entrada" ou "saida"
        'data_movimentacao', // Quando foi
    ];

    /**
     * Relacionamento: Uma movimentação pertence a UM produto
     * 
     * Cada registro de movimentação está vinculado a um único produto
     * 
     * Como usar:
     * $movimentacao = Estoque::find(1);
     * $produto = $movimentacao->produto;  // Qual produto foi movimentado
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'id_produto');
    }

    /**
     * Relacionamento: Uma movimentação pertence a UM usuário
     * 
     * Quem registrou essa movimentação?
     * 
     * NOTA: Removemos 'id_usuario' da $fillable porque não existe
     * esta coluna na tabela estoques (foi um erro no design original)
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
```

---

## ETAPA 3: CRIAR FILAMENT RESOURCES

### O que é um Filament Resource?
Um Resource é um "gerenciador" de uma entidade no painel admin. Inclui formulário, tabela lista, e ações.

### Como Criar:
```bash
php artisan make:filament-resource Usuario
php artisan make:filament-resource Produto
php artisan make:filament-resource Estoque
```

### PARTE 3A: FILAMENT FORM SCHEMAS

O Form Schema define QUAIS CAMPOS aparecem no formulário de criação/edição.

#### Form 1: UsuarioForm

**Arquivo:** `app/Filament/Resources/Usuarios/Schemas/UsuarioForm.php`

```php
<?php

namespace App\Filament\Resources\Usuarios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UsuarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                /**
                 * TextInput::make('nome')
                 * 
                 * Cria um campo de entrada de texto no formulário
                 * 
                 * 'nome' = qual coluna do banco será preenchida
                 * Deve coincidir com a $fillable do Model
                 * 
                 * ->required()
                 * = campo obrigatório (usuário DEVE preencher)
                 * Se deixar em branco, mostra erro "Campo obrigatório"
                 * 
                 * ->label('Nome do Usuário')
                 * = o texto que aparece acima do campo na UI
                 * Se não especificar, usa 'nome' em maiúscula
                 * 
                 * Exemplo de código HTML gerado (simplificado):
                 * <label>Nome do Usuário</label>
                 * <input type="text" name="nome" required>
                 */
                TextInput::make('nome')
                    ->required()
                    ->label('Nome do Usuário'),

                /**
                 * TextInput::make('login')
                 * 
                 * Campo para nome de usuário
                 * Este é o campo que o usuário usa para fazer login
                 */
                TextInput::make('login')
                    ->required()
                    ->label('Login'),

                /**
                 * TextInput::make('tipo_usuario')
                 * 
                 * IMPORTANTÍSSIMO: Use underscore (_), NÃO hífen (-)
                 * 
                 * ERRADO: TextInput::make('tipo-usuario') ← ERRO!
                 * CORRETO: TextInput::make('tipo_usuario') ← OK
                 * 
                 * Por quê? Porque no banco de dados a coluna é "tipo_usuario"
                 * Hífen não é válido em nomes de coluna SQL
                 * 
                 * Se usar hífen aqui, e o banco tem underscore,
                 * Laravel não encontra o campo e gera erro:
                 * "Field 'tipo_usuario' doesn't have a default value"
                 * 
                 * Isso porque Filament envia 'tipo-usuario' para o Model,
                 * Model procura 'tipo_usuario', não encontra, erra
                 */
                TextInput::make('tipo_usuario')
                    ->required()
                    ->label('Tipo de Usuário'),
            ]);
    }
}
```

#### Form 2: ProdutoForm

**Arquivo:** `app/Filament/Resources/Produtos/Schemas/ProdutoForm.php`

```php
<?php

namespace App\Filament\Resources\Produtos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class ProdutoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                /**
                 * Select::make('id_usuario')
                 * 
                 * Cria um dropdown (seleção) no formulário
                 * Ao invés de digitar, o usuário ESCOLHE da lista
                 * 
                 * ->relationship('usuario', 'nome')
                 * 
                 * = use o relacionamento 'usuario' do Model Produto
                 * Pega a coluna 'nome' para exibir como opção
                 * 
                 * Exemplo:
                 * - Select mostra: "Gabriel" e "Roberto" (nomes dos usuários)
                 * - Quando seleciona "Gabriel", salva id_usuario=2
                 * 
                 * Relacionamento definido em app/Models/Produto.php:
                 * public function usuario() {
                 *     return $this->belongsTo(Usuario::class, ...);
                 * }
                 * 
                 * Laravel automaticamente:
                 * 1. Pega o relacionamento 'usuario'
                 * 2. Query na tabela usuarios
                 * 3. Pega coluna 'nome' para cada usuário
                 * 4. Cria dropdown com essas opções
                 */
                Select::make('id_usuario')
                    ->relationship('usuario', 'nome')
                    ->required()
                    ->label('Dono do Produto'),

                TextInput::make('nome')
                    ->required()
                    ->label('Nome do Produto'),

                TextInput::make('marca')
                    ->required()
                    ->label('Marca'),

                TextInput::make('cor')
                    ->required()
                    ->label('Cor'),

                TextInput::make('textura')
                    ->required()
                    ->label('Textura'),

                /**
                 * TextInput::make('peso')
                 * 
                 * ->numeric()
                 * 
                 * Campo que aceita APENAS números
                 * Não permite letras, caracteres especiais
                 * 
                 * Útil para campos que devem ser números
                 * Evita erros como salvar "abc" quando deveria ser número
                 */
                TextInput::make('peso')
                    ->numeric()
                    ->required()
                    ->label('Peso'),

                TextInput::make('unidade_medida')
                    ->required()
                    ->label('Unidade de Medida'),

                TextInput::make('aplicacao')
                    ->required()
                    ->label('Aplicação'),

                TextInput::make('categoria')
                    ->required()
                    ->label('Categoria'),

                TextInput::make('temperatura_armazenamento')
                    ->required()
                    ->label('Temperatura de Armazenamento'),

                /**
                 * DatePicker::make('data_validade')
                 * 
                 * Campo que permite escolher uma DATA
                 * Mostra um calendário interativo
                 * 
                 * Usuário clica, aparece calendário, seleciona data
                 * Formato armazenado: YYYY-MM-DD
                 */
                DatePicker::make('data_validade')
                    ->required()
                    ->label('Data de Validade'),

                TextInput::make('estoque_minimo')
                    ->numeric()
                    ->required()
                    ->label('Estoque Mínimo'),
            ]);
    }
}
```

#### Form 3: EstoqueForm

**Arquivo:** `app/Filament/Resources/Estoques/Schemas/EstoqueForm.php`

```php
<?php

namespace App\Filament\Resources\Estoques\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class EstoqueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                /**
                 * Select::make('id_produto')
                 * 
                 * Dropdown de produtos
                 * Usuário seleciona qual produto está sendo movimentado
                 * 
                 * ->relationship('produto', 'nome')
                 * 
                 * Pega o relacionamento 'produto' do Model Estoque
                 * Mostra o 'nome' de cada produto na lista
                 */
                Select::make('id_produto')
                    ->relationship('produto', 'nome')
                    ->required()
                    ->label('Produto'),

                /**
                 * TextInput::make('estoque_atual')
                 * 
                 * ATENÇÃO: Este é um campo importante!
                 * 
                 * Armazena a quantidade APÓS a movimentação
                 * 
                 * Exemplo:
                 * - Tinha 100 unidades
                 * - Entrou 50 unidades
                 * - estoque_atual = 150
                 * 
                 * Este campo está na tabela estoques (migration)
                 * Deve estar na $fillable do Model
                 * Deve estar aqui no formulário
                 * 
                 * Se não sincronizar esses 3 lugares, erro!
                 */
                TextInput::make('estoque_atual')
                    ->numeric()
                    ->required()
                    ->label('Estoque Atual'),

                /**
                 * Select::make('movimentacao')
                 * 
                 * ->options([...])
                 * 
                 * Em vez de usar relacionamento, usa opções fixas
                 * 
                 * Opções:
                 * 'entrada' => 'Entrada'
                 * 
                 * 'entrada' = valor que será salvo no banco
                 * 'Entrada' = texto que aparece para o usuário
                 * 
                 * Quando usuário seleciona "Entrada", salva "entrada" no banco
                 * 
                 * Só há 2 opções: entrada ou saida
                 * Não precisa fazer query ao banco, valores são fixos
                 */
                Select::make('movimentacao')
                    ->options([
                        'entrada' => 'Entrada',
                        'saida' => 'Saída',
                    ])
                    ->required()
                    ->label('Tipo Movimentação'),

                /**
                 * DateTimePicker::make('data_movimentacao')
                 * 
                 * Como DatePicker, mas inclui HORA também
                 * Usuário seleciona data E hora
                 * 
                 * Exemplo: 2027-02-05 13:46:06
                 * Data: 2027-02-05
                 * Hora: 13:46:06
                 */
                DateTimePicker::make('data_movimentacao')
                    ->required()
                    ->label('Data Movimentação'),
            ]);
    }
}
```

### PARTE 3B: FILAMENT TABLE SCHEMAS

A Table Schema define QUAIS COLUNAS aparecem na listagem (tabela visual).

#### Table 1: UsuariosTable

**Arquivo:** `app/Filament/Resources/Usuarios/Tables/UsuariosTable.php`

```php
<?php

namespace App\Filament\Resources\Usuarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsuariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            /**
             * ->columns([...])
             * 
             * Define quais colunas aparecem na tabela listagem
             * 
             * Importante: Colunas aqui são INDEPENDENTES do formulário!
             * Você pode:
             * - Ter mais colunas na tabela que no formulário
             * - Ter menos colunas na tabela que no formulário
             * - Ter colunas diferentes
             * 
             * Exemplo:
             * Formulário: nome, login, tipo_usuario
             * Tabela: nome, login, tipo_usuario, created_at, updated_at
             */
            ->columns([
                /**
                 * TextColumn::make('nome')
                 * 
                 * Cria uma coluna na tabela que mostra o campo 'nome'
                 * 
                 * ->searchable()
                 * 
                 * Permite buscar por este campo
                 * Usuário digita "Gabriel" na barra de busca, encontra
                 * 
                 * Sem ->searchable(), campo não aparece nos resultados de busca
                 */
                TextColumn::make('nome')
                    ->searchable(),

                TextColumn::make('login')
                    ->searchable(),

                /**
                 * TextColumn::make('tipo_usuario')
                 * 
                 * ATENÇÃO: Use underscore (_), não hífen (-)
                 * 
                 * Se usar 'tipo-usuario', vai procurar por uma coluna
                 * que não existe no banco, e não mostra nada ou erro
                 */
                TextColumn::make('tipo_usuario')
                    ->searchable(),

                /**
                 * TextColumn::make('created_at')
                 * 
                 * ->dateTime()
                 * 
                 * Formata a coluna como data/hora
                 * Converte para formato legível
                 * 
                 * ->sortable()
                 * 
                 * Permite clicar no header para ordenar
                 * Clica em "Data", ordena crescente/decrescente
                 * 
                 * ->toggleable(isToggledHiddenByDefault: true)
                 * 
                 * Esta coluna fica OCULTA por padrão
                 * Usuário pode mostrar clicando em ícone de "colunas"
                 * 
                 * Útil para colunas que nem sempre você quer ver
                 * Exemplo: created_at, updated_at (não sempre útil)
                 */
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            /**
             * ->filters([...])
             * 
             * Define filtros que usuário pode usar
             * Exemplo: "Mostrar apenas usuários criados em junho"
             * 
             * Por enquanto vazio, mas pode adicionar mais tarde
             */
            ->filters([
                //
            ])

            /**
             * ->recordActions([...])
             * 
             * Ações que aparecem em cada linha
             * Exemplo: botão "Editar" em cada usuário
             */
            ->recordActions([
                /**
                 * EditAction::make()
                 * 
                 * Cria botão "Editar" em cada linha
                 * Clica, abre formulário para editar aquele usuário
                 */
                EditAction::make(),
            ])

            /**
             * ->toolbarActions([...])
             * 
             * Ações que aparecem na toolbar (acima da tabela)
             * Exemplo: botão "Deletar vários"
             */
            ->toolbarActions([
                BulkActionGroup::make([
                    /**
                     * DeleteBulkAction::make()
                     * 
                     * Permite selecionar VÁRIOS usuários (checkbox)
                     * Depois deletar todos de uma vez
                     * 
                     * Mais eficiente que deletar um por um
                     */
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

#### Table 2: ProdutosTable

**Arquivo:** `app/Filament/Resources/Produtos/Tables/ProdutosTable.php`

```php
<?php

namespace App\Filament\Resources\Produtos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProdutosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                /**
                 * TextColumn::make('nome')
                 * 
                 * Mostra o nome do produto na tabela
                 * ->searchable() permite buscar por nome
                 */
                TextColumn::make('nome')
                    ->searchable(),

                TextColumn::make('marca')
                    ->searchable(),

                TextColumn::make('cor')
                    ->searchable(),

                TextColumn::make('categoria')
                    ->searchable(),

                /**
                 * TextColumn::make('data_validade')
                 * 
                 * ->date()
                 * 
                 * Formata como data (só data, sem hora)
                 * Exemplo: "31/12/2027"
                 * 
                 * Sem formatação, mostraria: "2027-12-31 00:00:00"
                 */
                TextColumn::make('data_validade')
                    ->date(),

                /**
                 * TextColumn::make('estoque_minimo')
                 * 
                 * ->numeric()
                 * 
                 * Formata como número
                 * Alinha à direita, adiciona separadores de milhar
                 * Exemplo: 1.000 em vez de 1000
                 */
                TextColumn::make('estoque_minimo')
                    ->numeric(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

#### Table 3: EstoquesTable

**Arquivo:** `app/Filament/Resources/Estoques/Tables/EstoquesTable.php`

```php
<?php

namespace App\Filament\Resources\Estoques\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EstoquesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                /**
                 * TextColumn::make('id_estoque')
                 * 
                 * ->label('ID')
                 * 
                 * Muda o nome da coluna exibida
                 * "id_estoque" é muito longo, coloca só "ID"
                 */
                TextColumn::make('id_estoque')
                    ->label('ID'),

                /**
                 * TextColumn::make('produto.nome')
                 * 
                 * ATENÇÃO: Relacionamento com PONTO
                 * 
                 * 'produto.nome' significa:
                 * - Pegue o relacionamento 'produto' desta movimentação
                 * - Depois pegue o campo 'nome' do produto
                 * 
                 * Exemplo:
                 * Esta movimentação tem id_produto = 3
                 * Pega o Produto 3
                 * Pega o nome: "Coca-Cola"
                 * Mostra "Coca-Cola" na tabela
                 * 
                 * É como fazer um JOIN no SQL:
                 * SELECT estoques.*, produtos.nome
                 * FROM estoques
                 * JOIN produtos ON estoques.id_produto = produtos.id_produto
                 * 
                 * Laravel (e Filament) fazem isso automaticamente
                 * Você só usa 'produto.nome' e pronto
                 */
                TextColumn::make('produto.nome')
                    ->label('Produto')
                    ->searchable(),

                /**
                 * TextColumn::make('estoque_atual')
                 * 
                 * ->numeric()
                 * 
                 * Mostra o estoque formatado como número
                 */
                TextColumn::make('estoque_atual')
                    ->label('Estoque Atual')
                    ->numeric(),

                /**
                 * TextColumn::make('movimentacao')
                 * 
                 * Mostra se foi "entrada" ou "saida"
                 */
                TextColumn::make('movimentacao')
                    ->label('Movimentação'),

                /**
                 * TextColumn::make('data_movimentacao')
                 * 
                 * ->dateTime()
                 * 
                 * Formata como data/hora
                 * ->sortable() permite ordenar por data
                 */
                TextColumn::make('data_movimentacao')
                    ->label('Data Movimentação')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

---

## ERROS COMUNS E COMO EVITAR

### Erro 1: "Unknown column 'campo' in 'field list'"

**Causa:** Está tentando salvar um campo que não existe na tabela

**Solução:**
1. Verifique se a coluna existe na migration
2. Verifique se a migration foi executada (`php artisan migrate`)
3. Verifique se o campo está em `$fillable` do Model

**Checklist:**
```
Migration: $table->string('campo');  ✓
Model: 'campo' in $fillable array   ✓
Form: TextInput::make('campo')       ✓
```

---

### Erro 2: "Field 'campo' doesn't have a default value"

**Causa:** Campo obrigatório no banco não está sendo enviado

**Solução:**
1. Adicione o campo no formulário
2. Ou adicione default value na migration

**Exemplo:**
```php
// Migration
$table->string('tipo_usuario')->default('operador');

// Ou Form
TextInput::make('tipo_usuario')->required()
```

---

### Erro 3: "Unknown column 'usuarios.name' in 'field list'"

**Causa:** Está tentando referenciar um campo que não existe

**Solução:** Verifique o nome correto da coluna

```php
// ERRADO
->relationship('usuario', 'name')  // usuarios não tem "name"

// CORRETO
->relationship('usuario', 'nome')  // usuarios tem "nome"
```

---

### Erro 4: Colunas não aparecem na tabela

**Causa:** Não adicionou TextColumn na tabela

**Solução:** Preencha o array `->columns([ ])`

```php
// ERRADO
->columns([
    //
])

// CORRETO
->columns([
    TextColumn::make('nome'),
    TextColumn::make('email'),
])
```

---

### Erro 5: Typo no nome do campo (hífen vs underscore)

**Causa:** Banco usa underscore, mas código usa hífen

```php
// ERRADO
TextInput::make('tipo-usuario')  // Banco tem tipo_usuario

// CORRETO
TextInput::make('tipo_usuario')
```

---

### Erro 6: "typo 'cascateOnDelete()'"

**Causa:** Método deletar sem relação foi digitado errado

```php
// ERRADO
->cascateOnDelete()  // Falta 'd'

// CORRETO
->cascadeOnDelete()
```

---

## FLUXO COMPLETO DE DADOS

### Criar um novo Usuário (passo a passo)

```
1. USUÁRIO ACESSA: http://localhost:8000/admin/usuarios
   ↓
2. CLICA BOTÃO: "Criar usuário"
   ↓
3. FILAMENT CARREGA: EstoqueForm.php
   ↓
   TextInput::make('nome')
   TextInput::make('login')
   TextInput::make('tipo_usuario')
   ↓
4. USUÁRIO PREENCHE:
   Nome: Gabriel
   Login: gabriel123
   Tipo: operador
   ↓
5. CLICA: "Salvar"
   ↓
6. FILAMENT ENVIA POST para Controller:
   {
     'nome': 'Gabriel',
     'login': 'gabriel123',
     'tipo_usuario': 'operador'
   }
   ↓
7. CONTROLLER EXECUTA:
   Usuario::create($dados)
   ↓
8. MODEL VALIDA:
   - Campo 'nome' está em $fillable? ✓
   - Campo 'login' está em $fillable? ✓
   - Campo 'tipo_usuario' está em $fillable? ✓
   ↓
9. ELOQUENT EXECUTA SQL:
   INSERT INTO usuarios (nome, login, tipo_usuario, created_at, updated_at)
   VALUES ('Gabriel', 'gabriel123', 'operador', NOW(), NOW())
   ↓
10. BANCO DE DADOS:
    Salva na tabela usuarios
    Auto-incrementa id_usuario (ex: 2)
    ↓
11. FILAMENT REDIRECIONA para lista:
    http://localhost:8000/admin/usuarios
    ↓
12. FILAMENT CARREGA: UsuariosTable.php
    ↓
    Query: SELECT * FROM usuarios
    ↓
13. FILAMENT RENDERIZA:
    TextColumn::make('nome') → mostra Gabriel
    TextColumn::make('login') → mostra gabriel123
    TextColumn::make('tipo_usuario') → mostra operador
    ↓
14. USUÁRIO VÊ: Tabela com novo registro
```

---

## RESUMO: PONTOS CRÍTICOS

### 1. Sincronização Nome de Campos

```
Migration:   $table->string('tipo_usuario')
Model:       'tipo_usuario' in $fillable
Form:        TextInput::make('tipo_usuario')
Table:       TextColumn::make('tipo_usuario')

SE NÃO SINCRONIZAR, VAI DAR ERRO!
```

### 2. Foreign Keys

```
Migration:
$table->foreignId('id_usuario')
    ->constrained('usuarios','id_usuario')
    ->cascadeOnDelete()

Model:
public function usuario() {
    return $this->belongsTo(Usuario::class, ...);
}

Form/Table:
->relationship('usuario', 'nome')
```

### 3. $fillable é OBRIGATÓRIO

```
ERRADO: Esquecer de adicionar campo em $fillable
CORRETO: Adicionar TODOS os campos que o formulário envia

$fillable = [
    'id_produto',
    'estoque_atual',
    'movimentacao',
    'data_movimentacao',
]
```

### 4. Sempre Sincronizar 3 Lugares

```
Quando criar um novo campo:

1. Migration (criar coluna):
   $table->string('novo_campo');

2. Model ($fillable):
   protected $fillable = [..., 'novo_campo'];

3. Form/Table (interface):
   TextInput::make('novo_campo')
   TextColumn::make('novo_campo')

Se falta qualquer um → ERRO
```

---

## COMANDOS ÚTEIS

```bash
# Criar arquivos
php artisan make:migration create_tabela_table
php artisan make:model Tabela
php artisan make:filament-resource Tabela

# Executar migrations
php artisan migrate              # Executar
php artisan migrate:rollback     # Voltar atrás
php artisan migrate:refresh      # Volta tudo + executa novamente

# Limpar caches
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear

# Debug
php artisan tinker              # Console interativo
Usuario::all();                 # Ver todos os usuários
Produto::find(1);              # Ver produto 1
```

---

**FIM DO GUIA COMPLETO**

Agora você tem tudo documentado e comentado para criar seu próprio projeto do zero!
