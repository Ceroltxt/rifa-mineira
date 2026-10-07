@extends('layouts.rifa')

@section('title', 'Sem Rifa Ativa')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="text-6xl mb-4">🧺</div>
    <h2 class="text-2xl font-bold text-amber-700 mb-2">Nenhuma rifa ativa no momento</h2>
    <p class="text-gray-500 mb-6">Fique de olho! Em breve uma nova rifa estará disponível.</p>
    <a href="{{ route('admin.rifa.index') }}"
       class="bg-amber-700 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-amber-800 transition-all">
        Área Admin →
    </a>
</div>
@endsection
