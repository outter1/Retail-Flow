<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estoques', function (Blueprint $table) {
            $table->id('id_estoque');

            $table->foreignId('id_produto')
            ->constrained('produtos','id_produto')
            ->cascadeOnDelete();

            $table->integer('estoque_atual');
            $table->string('movimentacao');
            $table->date('data_movimentacao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoques');
    }
};
