<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table = 'produtos';

    protected $primaryKey = 'id_produto';

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

    /*
    CARDINALIDADE:
    PRODUTO N:1 USUÁRIO

    Muitos produtos podem pertencer a um mesmo usuário.
    Mas cada produto pertence a apenas um usuário.

    Exemplo:
    Produto "Coca-Cola" pertence ao usuário Gabriel.
    Produto "Salgado" pertence ao usuário Gabriel.
    Produto "Doce" pertence ao usuário Roberto.

    Como o produto tem a foreign key id_usuario,
    usamos belongsTo.
    */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /*
    CARDINALIDADE:
    PRODUTO 1:N ESTOQUE

    Um produto pode ter várias movimentações de estoque.
    Mas cada movimentação pertence a apenas um produto.

    Exemplo:
    Produto "Coca-Cola" pode ter:
    - Entrada de 50 unidades
    - Saída de 10 unidades
    - Saída de 5 unidades

    Por isso usamos hasMany.
    */
    public function estoques()
    {
        return $this->hasMany(Estoque::class, 'id_produto');
    }
}