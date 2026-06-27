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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id('id_produto');

            $table->foreignId('id_usuario')
            ->constrained('usuarios','id_usuario')
            ->cascadeOnDelete();

            $table->string('nome');
            $table->string('marca');
            $table->string('cor');
            $table->string('textura');
            $table->decimal('peso',10,3);
            $table->string('unidade_medida');
            $table->string('aplicacao');
            $table->string('categoria');
            $table->string('temperatura_armazenamento');
            $table->date('data_validade');
            $table->integer('estoque_minimo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
