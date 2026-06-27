<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nome',
        'login',
        'tipo_usuario',
    ];

    /*
    CARDINALIDADE:
    USUÁRIO 1:N PRODUTO

    Um usuário pode cadastrar vários produtos.
    Mas cada produto pertence a apenas um usuário.

    Exemplo:
    Usuario Gabriel pode cadastrar:
    - Produto 1
    - Produto 2
    - Produto 3

    Por isso usamos hasMany.
    */
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'id_usuario', 'id_usuario');
    }

    /*
    CARDINALIDADE:
    USUÁRIO 1:N ESTOQUE

    Um usuário pode registrar várias movimentações de estoque.
    Mas cada movimentação de estoque pertence a apenas um usuário.

    Exemplo:
    Usuario Gabriel registrou:
    - Entrada de 10 produtos
    - Saída de 5 produtos
    - Entrada de 20 produtos

    Por isso usamos hasMany.
    */
    public function estoques()
    {
        return $this->hasMany(Estoque::class, 'id_usuario');
    }
}