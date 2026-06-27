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
                Select::make('id_produto')
                    ->relationship('produto', 'nome')
                    ->required()
                    ->label('Produto'),
                TextInput::make('estoque_atual')
                    ->numeric()
                    ->required()
                    ->label('Estoque Atual'),
                Select::make('movimentacao')
                    ->options([
                        'entrada' => 'Entrada',
                        'saida' => 'Saída',
                    ])
                    ->required()
                    ->label('Tipo Movimentação'),
                DateTimePicker::make('data_movimentacao')
                    ->required()
                    ->label('Data Movimentação'),
            ]);
    }
}
