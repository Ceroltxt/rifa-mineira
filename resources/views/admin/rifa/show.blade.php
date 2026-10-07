@extends('layouts.rifa')

@section('title', 'Admin — ' . $rifa->titulo)

@section('content')
<div class="flex items-center gap-3 mb-6 flex-wrap">
    <a href="{{ route('admin.rifa.index') }}" class="text-amber-700 hover:underline text-sm">← Todas as rifas</a>
    <h2 class="text-xl font-bold text-gray-800">{{ $rifa->titulo }}</h2>
    @php
        $badge = match($rifa->status) {
            'ativa'     => 'bg-green-100 text-green-700 border-green-200',
            'encerrada' => 'bg-gray-100 text-gray-600 border-gray-200',
            'sorteada'  => 'bg-amber-100 text-amber-700 border-amber-200',
        };
    @endphp
    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full border {{ $badge }}">
        {{ ucfirst($rifa->status) }}
    </span>
</div>

{{-- Cards de estatísticas --}}
@php
    $pendentesCount = $rifa->bilhetes()->where('status', 'pendente')->count();
@endphp
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-amber-200 p-4 text-center shadow-sm">
        <p class="text-gray-500 text-xs mb-1">Total de bilhetes</p>
        <p class="text-2xl font-bold text-gray-800">{{ $rifa->total_bilhetes }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-amber-200 p-4 text-center shadow-sm">
        <p class="text-gray-500 text-xs mb-1">Confirmados ✅</p>
        <p class="text-2xl font-bold text-green-600">{{ $vendidos }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-amber-200 p-4 text-center shadow-sm">
        <p class="text-gray-500 text-xs mb-1">Aguardando ⏳</p>
        <p class="text-2xl font-bold text-orange-500">{{ $pendentesCount }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-amber-200 p-4 text-center shadow-sm">
        <p class="text-gray-500 text-xs mb-1">Arrecadado</p>
        <p class="text-2xl font-bold text-amber-700">R$ {{ number_format($vendidos * $rifa->preco_bilhete, 2, ',', '.') }}</p>
    </div>
</div>

{{-- Progresso --}}
<div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-5 mb-6">
    @php $pct = $rifa->percentVendido(); @endphp
    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
        <span>Progresso (confirmados)</span>
        <span>{{ $pct }}% vendido</span>
    </div>
    <div class="h-4 rounded-full bg-amber-100 overflow-hidden">
        <div class="h-full bg-amber-600 rounded-full transition-all" style="width: {{ $pct }}%"></div>
    </div>
</div>

{{-- Ganhador (se já sorteado) --}}
@if($rifa->status === 'sorteada' && $rifa->ganhador)
<div class="bg-amber-50 border-2 border-amber-400 rounded-2xl p-5 mb-6 text-center">
    <div class="text-4xl mb-2">🏆</div>
    <p class="font-bold text-xl text-amber-800">{{ $rifa->ganhador->comprador_nome }}</p>
    <p class="text-amber-600">Bilhete #{{ str_pad($rifa->ganhador->numero, 2, '0', STR_PAD_LEFT) }}</p>
    @if($rifa->ganhador->comprador_telefone)
        <p class="text-amber-600 text-sm mt-1">📱 {{ $rifa->ganhador->comprador_telefone }}</p>
    @endif
</div>
@endif

{{-- Ações --}}
@if($rifa->status === 'ativa')
<div class="flex gap-3 mb-6 flex-wrap">
    <form method="POST" action="{{ route('admin.rifa.sortear', $rifa) }}"
          onsubmit="return confirm('Tem certeza? O sorteio considera apenas bilhetes CONFIRMADOS (pagos). Irreversível!')">
        @csrf
        <button type="submit"
                class="bg-amber-700 hover:bg-amber-800 text-white font-bold px-6 py-2.5 rounded-xl shadow transition-all hover:scale-105">
            🎲 Realizar Sorteio
        </button>
    </form>
    <form method="POST" action="{{ route('admin.rifa.encerrar', $rifa) }}"
          onsubmit="return confirm('Encerrar a rifa sem sortear?')">
        @csrf
        <button type="submit"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-6 py-2.5 rounded-xl transition-all">
            ⛔ Encerrar Rifa
        </button>
    </form>
</div>
@endif

{{-- ===== COMPRAS PENDENTES (aguardando validação) ===== --}}
@php
    $comprasPendentes = $rifa->compras()->where('status', 'pendente')->with('bilhetes')->latest()->get();
@endphp

@if($comprasPendentes->isNotEmpty())
<div class="bg-white rounded-2xl border-2 border-orange-300 shadow-sm p-5 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <span class="text-xl">⏳</span>
        <h3 class="font-bold text-base text-orange-700">
            Aguardando Validação ({{ $comprasPendentes->count() }})
        </h3>
        <span class="text-xs text-gray-400 ml-auto">Valide após receber o comprovante no WhatsApp</span>
    </div>

    <div class="space-y-3">
        @foreach($comprasPendentes as $compra)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3
                    border border-orange-200 bg-orange-50 rounded-xl p-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="font-semibold text-gray-800">{{ $compra->comprador_nome }}</p>
                    <span class="text-xs text-gray-400">{{ $compra->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-500 mt-0.5">📱 {{ $compra->comprador_telefone }}</p>
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach($compra->bilhetes as $b)
                        <span class="inline-block bg-orange-200 text-orange-800 text-xs px-2 py-0.5 rounded font-mono font-bold">
                            #{{ str_pad($b->numero, 2, '0', STR_PAD_LEFT) }}
                        </span>
                    @endforeach
                    <span class="text-xs text-gray-500 self-center ml-1">
                        · R$ {{ number_format($compra->valor_total, 2, ',', '.') }}
                        · {{ ucfirst($compra->forma_pagamento) }}
                    </span>
                </div>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                {{-- Abrir WhatsApp com a pessoa --}}
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $compra->comprador_telefone) }}"
                   target="_blank"
                   class="bg-green-100 hover:bg-green-200 text-green-700 font-bold px-3 py-2 rounded-xl text-sm transition-all"
                   title="Abrir WhatsApp">
                    📲
                </a>
                {{-- Validar --}}
                <form method="POST"
                      action="{{ route('admin.rifa.compra.validar', [$rifa, $compra]) }}"
                      onsubmit="return confirm('Confirmar pagamento de {{ $compra->comprador_nome }}?')">
                    @csrf
                    <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-xl text-sm transition-all">
                        ✅ Validar
                    </button>
                </form>
                {{-- Rejeitar --}}
                <form method="POST"
                      action="{{ route('admin.rifa.compra.rejeitar', [$rifa, $compra]) }}"
                      onsubmit="return confirm('Rejeitar compra de {{ $compra->comprador_nome }}? Os bilhetes voltarão para disponível.')">
                    @csrf
                    <button type="submit"
                            class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-4 py-2 rounded-xl text-sm transition-all">
                        ❌ Rejeitar
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ===== COMPRAS CONFIRMADAS ===== --}}
<div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-5">
    <h3 class="font-bold text-base text-gray-800 mb-4">
        ✅ Pagamentos Confirmados ({{ $compras->total() }})
    </h3>

    @if($compras->isEmpty())
        <p class="text-gray-400 text-sm text-center py-6">Nenhum pagamento confirmado ainda.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-amber-100 text-gray-500 text-xs uppercase">
                        <th class="text-left py-2 pr-4">Comprador</th>
                        <th class="text-left py-2 pr-4">WhatsApp</th>
                        <th class="text-left py-2 pr-4">Bilhetes</th>
                        <th class="text-left py-2 pr-4">Total</th>
                        <th class="text-left py-2">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-50">
                    @foreach($compras as $compra)
                    <tr class="hover:bg-amber-50 transition-colors">
                        <td class="py-2.5 pr-4 font-medium text-gray-800">{{ $compra->comprador_nome }}</td>
                        <td class="py-2.5 pr-4 text-gray-500">
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $compra->comprador_telefone) }}"
                               target="_blank" class="hover:text-green-600 transition-colors">
                                {{ $compra->comprador_telefone }}
                            </a>
                        </td>
                        <td class="py-2.5 pr-4">
                            @foreach($compra->bilhetes as $b)
                                <span class="inline-block bg-amber-100 text-amber-800 text-xs px-1.5 py-0.5 rounded font-mono">
                                    #{{ str_pad($b->numero, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="py-2.5 pr-4 font-semibold text-amber-700">
                            R$ {{ number_format($compra->valor_total, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 text-gray-400 text-xs">{{ $compra->created_at->format('d/m H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $compras->links() }}</div>
    @endif
</div>
@endsection
