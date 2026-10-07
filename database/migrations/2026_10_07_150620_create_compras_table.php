<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rifa_id')->constrained()->cascadeOnDelete();
            $table->string('comprador_nome');
            $table->string('comprador_email')->nullable();
            $table->string('comprador_telefone');
            $table->integer('total_bilhetes');
            $table->decimal('valor_total', 8, 2);
            $table->enum('forma_pagamento', ['pix', 'cartao', 'boleto'])->default('pix');
            $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
