<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rifa extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'preco_bilhete',
        'total_bilhetes',
        'status',
        'data_sorteio',
        'ganhador_bilhete_id',
    ];

    protected $casts = [
        'preco_bilhete'   => 'float',
        'total_bilhetes'  => 'integer',
        'data_sorteio'    => 'datetime',
    ];

    public function bilhetes(): HasMany
    {
        return $this->hasMany(Bilhete::class);
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    public function ganhador()
    {
        return $this->belongsTo(Bilhete::class, 'ganhador_bilhete_id');
    }

    // Contadores úteis
    public function totalVendidos(): int
    {
        return $this->bilhetes()->where('status', 'pago')->count();
    }

    public function totalDisponiveis(): int
    {
        return $this->total_bilhetes - $this->totalVendidos();
    }

    public function percentVendido(): int
    {
        $vendidos = $this->totalVendidos();
        if ($this->total_bilhetes === 0) return 0;
        return (int) round(($vendidos / $this->total_bilhetes) * 100);
    }

    public function isAtiva(): bool
    {
        return $this->status === 'ativa';
    }
}
