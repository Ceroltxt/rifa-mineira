<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    protected $fillable = [
        'rifa_id',
        'comprador_nome',
        'comprador_email',
        'comprador_telefone',
        'total_bilhetes',
        'valor_total',
        'forma_pagamento',
        'status',
    ];

    public function rifa(): BelongsTo
    {
        return $this->belongsTo(Rifa::class);
    }

    public function bilhetes(): HasMany
    {
        return $this->hasMany(Bilhete::class);
    }
}
