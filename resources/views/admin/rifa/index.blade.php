@extends('layouts.rifa')

@section('title', 'Admin — Rifas')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">⚙️ Painel Admin</h2>
        <p class="text-gray-500 text-sm">Gerencie todas as rifas</p>
    </div>
    <a href="{{ route('admin.rifa.create') }}"
       class="bg-amber-700 hover:bg-amber-800 text-white font-bold px-5 py-2.5 rounded-xl shadow transition-all hover:scale-105">
        + Nova Rifa
    </a>
</div>

@if($rifas->isEmpty())
    <div class="bg-white rounded-2xl border border-amber-200 p-12 text-center">
        <div class="text-5xl mb-3">🎟️</div>
        <p class="text-gray-500">Nenhuma rifa criada ainda.</p>
        <a href="{{ route('admin.rifa.create') }}" class="inline-block mt-4 text-amber-700 font-semibold underline">
            Criar primeira rifa →
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach($rifas as $rifa)
        <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-lg text-gray-800">{{ $rifa->titulo }}</h3>
                        @php
                            $badge = match($rifa->status) {
                                'ativa'     => 'bg-green-100 text-green-700 border-green-200',
                                'encerrada' => 'bg-gray-100 text-gray-600 border-gray-200',
                                'sorteada'  => 'bg-amber-100 text-amber-700 border-amber-200',
                            };
                            $icon = match($rifa->status) {
                                'ativa'     => '🟢',
                                'encerrada' => '⛔',
                                'sorteada'  => '🏆',
                            };
                        @endphp
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full border {{ $badge }}">
                            {{ $icon }} {{ ucfirst($rifa->status) }}
                        </span>
                    </div>

                    {{-- Barra de progresso --}}
                    @php
                        $pct = $rifa->total_bilhetes > 0
                            ? (int) round(($rifa->vendidos_count / $rifa->total_bilhetes) * 100)
                            : 0;
                    @endphp
                    <div class="flex items-center gap-3 mt-2">
                        <div class="flex-1 h-2 rounded-full bg-amber-100 overflow-hidden">
                            <div class="h-full bg-amber-600 rounded-full transition-all"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 whitespace-nowrap">
                            {{ $rifa->vendidos_count }}/{{ $rifa->total_bilhetes }} ({{ $pct }}%)
                        </span>
                    </div>

                    <div class="flex gap-4 mt-2 text-xs text-gray-500">
                        <span>💰 R$ {{ number_format($rifa->preco_bilhete, 2, ',', '.') }}/bilhete</span>
                        <span>💵 Arrecadado: R$ {{ number_format($rifa->vendidos_count * $rifa->preco_bilhete, 2, ',', '.') }}</span>
                        @if($rifa->data_sorteio)
                            <span>📅 {{ $rifa->data_sorteio->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.rifa.show', $rifa) }}"
                       class="bg-amber-100 hover:bg-amber-200 text-amber-800 font-semibold px-4 py-2 rounded-xl text-sm transition-all">
                        Ver detalhes
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

<div class="mt-8 border-t border-amber-100 pt-4">
    <a href="{{ route('rifa.index') }}" class="text-amber-700 text-sm hover:underline">← Ver site público</a>
</div>
@endsection
