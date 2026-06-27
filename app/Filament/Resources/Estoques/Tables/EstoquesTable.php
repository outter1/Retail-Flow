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
                TextColumn::make('id_estoque')
                    ->label('ID'),
                TextColumn::make('produto.nome')
                    ->label('Produto')
                    ->searchable(),
                TextColumn::make('estoque_atual')
                    ->label('Estoque Atual')
                    ->numeric(),
                TextColumn::make('movimentacao')
                    ->label('Movimentação'),
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
