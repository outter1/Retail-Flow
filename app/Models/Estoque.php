<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    protected $table = 'estoques';

    protected $primaryKey = 'id_estoque';

    protected $fillable = [
        'id_produto',
        'estoque_atual',
        'movimentacao',
        'data_movimentacao',
    ];

    /*
    CARDINALIDADE:
    ESTOQUE N:1 PRODUTO

    Muitas movimentações de estoque podem pertencer ao mesmo produto.
    Mas cada movimentação pertence a apenas um produto.

    Exemplo:
    Estoque ID 1 pertence ao produto Coca-Cola.
    Estoque ID 2 também pertence ao produto Coca-Cola.
    Estoque ID 3 pertence ao produto Salgado.

    Como estoque tem a foreign key id_produto,
    usamos belongsTo.
    */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    /*
    CARDINALIDADE:
    ESTOQUE N:1 USUÁRIO

    Muitas movimentações de estoque podem ser registradas pelo mesmo usuário.
    Mas cada movimentação pertence a apenas um usuário responsável.z

    Exemplo:
    Estoque ID 1 foi registrado por Gabriel.
    Estoque ID 2 foi registrado por Gabriel.
    Estoque ID 3 foi registrado por Roberto.

    Como estoque tem a foreign key id_usuario,
    usamos belongsTo.
    */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}