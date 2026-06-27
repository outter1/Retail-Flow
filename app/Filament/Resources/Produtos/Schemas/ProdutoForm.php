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
                Select::make('id_usuario')
                    ->relationship('usuario', 'nome')
                    ->required()
                    ->label('Usuário'),
                TextInput::make('nome')
                    ->required(),
                TextInput::make('marca')
                    ->required(),
                TextInput::make('cor')
                    ->required(),
                TextInput::make('textura')
                    ->required(),
                TextInput::make('peso')
                    ->numeric()
                    ->required(),
                TextInput::make('unidade_medida')
                    ->required(),
                TextInput::make('aplicacao')
                    ->required(),
                TextInput::make('categoria')
                    ->required(),
                TextInput::make('temperatura_armazenamento')
                    ->required(),
                DatePicker::make('data_validade')
                    ->required(),
                TextInput::make('estoque_minimo')
                    ->numeric()
                    ->required(),
            ]);
    }
}
