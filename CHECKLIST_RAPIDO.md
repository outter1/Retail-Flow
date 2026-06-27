# CHECKLIST RÁPIDO: Como Evitar Erros

## Ao Criar um Novo Campo/Atributo

Use este checklist para SEMPRE sincronizar corretamente:

### ✓ Passo 1: Adicione à Migration

```php
// database/migrations/XXXX_XX_XX_XXXXXX_create_tabela_table.php

Schema::create('tabela', function (Blueprint $table) {
    $table->id('id_tabela');
    
    // NOVO CAMPO
    $table->string('novo_campo');  // Escolha o tipo: string, integer, date, etc
    
    $table->timestamps();
});
```

**Tipos comuns:**
- `string('campo')` - Texto até 255 caracteres
- `integer('campo')` - Número inteiro
- `decimal('campo', 8, 2)` - Número com decimais (ex: 123.45)
- `date('campo')` - Data (YYYY-MM-DD)
- `dateTime('campo')` - Data com hora
- `boolean('campo')` - Verdadeiro/Falso
- `foreignId('campo')` - Chave estrangeira

---

### ✓ Passo 2: Adicione ao Model ($fillable)

```php
// app/Models/Tabela.php

class Tabela extends Model
{
    protected $table = 'tabela';
    protected $primaryKey = 'id_tabela';
    
    protected $fillable = [
        'campo1',
        'campo2',
        'novo_campo',  // ADICIONE AQUI
    ];
}
```

**IMPORTANTE:**
- Adicione TODOS os campos que o formulário vai enviar
- Se esquecer, Laravel ignora silenciosamente o valor
- Resultado: Campo não é salvo no banco

---

### ✓ Passo 3: Adicione ao Formulário (Schema)

```php
// app/Filament/Resources/Tabela/Schemas/TabelaForm.php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TabelaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('campo1'),
                TextInput::make('campo2'),
                
                // NOVO CAMPO
                TextInput::make('novo_campo')  // Use o mesmo nome
                    ->required()
                    ->label('Novo Campo'),
            ]);
    }
}
```

**Componentes disponíveis:**
- `TextInput::make('campo')` - Campo de texto
- `TextInput::make('campo')->numeric()` - Campo numérico
- `DatePicker::make('campo')` - Seletor de data
- `DateTimePicker::make('campo')` - Seletor de data/hora
- `Select::make('campo')->options([...])` - Dropdown com opções fixas
- `Select::make('campo')->relationship(...)` - Dropdown com dados do banco
- `Toggle::make('campo')` - Checkbox on/off

---

### ✓ Passo 4: Adicione à Tabela (Columns)

```php
// app/Filament/Resources/Tabela/Tables/TabelaTable.php

use Filament\Tables\Columns\TextColumn;

class TabelaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campo1'),
                TextColumn::make('campo2'),
                
                // NOVO CAMPO
                TextColumn::make('novo_campo')
                    ->label('Novo Campo'),
            ])
            ...
    }
}
```

**Formatadores de coluna:**
- `->label('Nome')` - Nome exibido no header
- `->searchable()` - Permite buscar neste campo
- `->sortable()` - Permite clicar para ordenar
- `->date()` - Formata como data
- `->dateTime()` - Formata como data/hora
- `->numeric()` - Formata como número
- `->toggleable(isToggledHiddenByDefault: true)` - Coluna oculta por padrão
- `.relationship('relacao', 'campo')` - Para mostrar dados relacionados

---

## Checklist de Nomes (MUITO IMPORTANTE!)

### Use UNDERSCORE (_), NÃO hífen (-)

```
✓ CORRETO
id_usuario
tipo_usuario
data_criacao
temperatura_armazenamento

✗ ERRADO
id-usuario
tipo-usuario
data-criacao
temperatura-armazenamento
```

**Por quê?**
- Banco de dados não aceita hífen em nomes de coluna
- Se usar hífen no código, Laravel não encontra a coluna

---

## Checklist Antes de Executar `php artisan migrate`

- [ ] Arquivo migration criado?
- [ ] Nome da coluna usa underscore (_)?
- [ ] Tipo de dados correto? (string, integer, etc)
- [ ] Relacionamentos (foreignId) apontam para a tabela certa?
- [ ] Foreign keys usam `->cascadeOnDelete()` quando faz sentido?

---

## Checklist Antes de Testar Formulário

- [ ] Model tem `$table = 'nome_correto'`?
- [ ] Model tem `$primaryKey = 'id_correto'`?
- [ ] TODOS os campos do formulário estão em `$fillable`?
- [ ] Nome dos campos usa underscore (_)?
- [ ] Foreign keys têm relacionamento definido no Model?

---

## Checklist Antes de Recarregar Página

**SEMPRE execute estes comandos após alterar código:**

```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

**Por quê?**
- Laravel cacheia (guarda em memória) para ser mais rápido
- Se não limpar, carrega código antigo em vez do novo

---

## Erros Comuns com Soluções Rápidas

### ❌ "Unknown column 'X' in 'field list'"

**Significado:** Coluna não existe na tabela

**Solução:**
1. `php artisan migrate` - executou todas as migrations?
2. Na migration, coluna está definida?
3. No Model, está em $fillable?

---

### ❌ "Field 'X' doesn't have a default value"

**Significado:** Campo obrigatório não foi preenchido

**Solução:**
1. Adicione o campo no formulário
2. Ou faça coluna opcional: `$table->string('campo')->nullable()`

---

### ❌ "Unknown column 'usuarios.name'"

**Significado:** Está usando nome de coluna errado

**Solução:**
```php
// ERRADO - usuarios tem 'nome', não 'name'
->relationship('usuario', 'name')

// CORRETO
->relationship('usuario', 'nome')
```

---

### ❌ Coluna não aparece na tabela

**Significado:** Não adicionou TextColumn

**Solução:**
```php
// Adicione em ->columns([...])
TextColumn::make('campo_que_desaparece')
```

---

### ❌ Form enviado mas dados não salvam

**Significado:** Campo não está em $fillable

**Solução:**
```php
protected $fillable = [
    'campo1',
    'campo_que_nao_salva',  // Adicione aqui
];
```

---

## Estrutura de Pastas Rápida

```
app/
├── Models/
│   ├── Usuario.php
│   ├── Produto.php
│   └── Estoque.php
│
└── Filament/
    └── Resources/
        ├── Usuarios/
        │   ├── UsuarioResource.php
        │   ├── Schemas/
        │   │   └── UsuarioForm.php
        │   ├── Tables/
        │   │   └── UsuariosTable.php
        │   └── Pages/
        │       ├── CreateUsuario.php
        │       ├── EditUsuario.php
        │       └── ListUsuarios.php
        │
        ├── Produtos/
        │   ├── ProdutoResource.php
        │   ├── Schemas/
        │   │   └── ProdutoForm.php
        │   ├── Tables/
        │   │   └── ProdutosTable.php
        │   └── Pages/
        │       ├── CreateProduto.php
        │       ├── EditProduto.php
        │       └── ListProdutos.php
        │
        └── Estoques/
            ├── EstoqueResource.php
            ├── Schemas/
            │   └── EstoqueForm.php
            ├── Tables/
            │   └── EstoquesTable.php
            └── Pages/
                ├── CreateEstoque.php
                ├── EditEstoque.php
                └── ListEstoques.php

database/
└── migrations/
    ├── 2026_06_23_024108_create_usuarios_table.php
    ├── 2026_06_23_024118_create_produtos_table.php
    └── 2026_06_23_024126_create_estoques_table.php
```

---

## Ordem Correta Para Criar Tudo do Zero

1. **Crie as Migrations** → Define estrutura do banco
2. **Execute `php artisan migrate`** → Cria as tabelas
3. **Crie os Models** → Define relacionamentos
4. **Crie os Filament Resources** → Interface admin
5. **Preencha Schemas (Forms)** → Formulários
6. **Preencha Tables** → Listagem
7. **Teste tudo** → Criar, editar, deletar
8. **Limpe caches** → Se algo não aparecer

---

## Exemplo Completo: Campo Novo "Descricao"

### 1️⃣ Migration

```php
$table->string('descricao')->nullable();
```

### 2️⃣ Model

```php
protected $fillable = [
    ...,
    'descricao',  // NOVO
];
```

### 3️⃣ Form

```php
TextInput::make('descricao')
    ->label('Descrição')
    ->nullable(),
```

### 4️⃣ Table

```php
TextColumn::make('descricao'),
```

### 5️⃣ Execute

```bash
php artisan migrate
php artisan view:clear && php artisan config:clear && php artisan cache:clear
```

Pronto! Novo campo funcionando!

---

## Comandos Essenciais

```bash
# Criar
php artisan make:migration create_tabela_table
php artisan make:model Tabela
php artisan make:filament-resource Tabela

# Migrar
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh

# Limpar (SEMPRE após alterar código!)
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# Debug
php artisan tinker
>>> Usuario::all();
>>> exit()

# Iniciar servidor
php artisan serve
```

---

**Salve este arquivo e consulte sempre que criar novos campos!**
