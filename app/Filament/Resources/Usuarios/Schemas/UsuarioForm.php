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
                TextInput::make('nome')
                    ->required(),
                TextInput::make('login')
                    ->required(),
                TextInput::make('tipo_usuario')
                    ->required(),
            ]);
    }
}
