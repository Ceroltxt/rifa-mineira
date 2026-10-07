<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bilhetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rifa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('compra_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('numero');
            $table->enum('status', ['disponivel', 'pendente', 'pago'])->default('disponivel');
            $table->string('comprador_nome')->nullable();
            $table->string('comprador_email')->nullable();
            $table->string('comprador_telefone')->nullable();
            $table->timestamps();

            $table->unique(['rifa_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bilhetes');
    }
};
