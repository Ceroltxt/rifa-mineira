<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rifas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->decimal('preco_bilhete', 8, 2)->default(5.00);
            $table->integer('total_bilhetes')->default(100);
            $table->enum('status', ['ativa', 'encerrada', 'sorteada'])->default('ativa');
            $table->dateTime('data_sorteio')->nullable();
            $table->foreignId('ganhador_bilhete_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rifas');
    }
};
