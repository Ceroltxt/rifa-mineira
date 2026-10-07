<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bilhete extends Model
{
    protected $fillable = [
        'rifa_id',
        'compra_id',
        'numero',
        'status',
        'comprador_nome',
        'comprador_email',
        'comprador_telefone',
    ];

    public function rifa(): BelongsTo
    {
        return $this->belongsTo(Rifa::class);
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }
}
