@extends('layouts.rifa')

@section('title', 'Nova Rifa')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.rifa.index') }}" class="text-amber-700 hover:underline text-sm">← Voltar</a>
        <h2 class="text-2xl font-bold text-gray-800">🎟️ Nova Rifa</h2>
    </div>

    <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.rifa.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título da Rifa *</label>
                <input type="text" name="titulo" required value="{{ old('titulo', 'Cesta de Produtos Mineiros') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all" />
                @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição do Prêmio</label>
                <textarea name="descricao" rows="3"
                          placeholder="Descreva o prêmio da rifa..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">{{ old('descricao', 'Queijo Frescal, Doce de Leite, Rapadura, Requeijão, Café Especial e Biscoito de Polvilho.') }}</textarea>
                @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço por bilhete (R$) *</label>
                    <input type="number" name="preco_bilhete" required step="0.01" min="0.01"
                           value="{{ old('preco_bilhete', '5.00') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all" />
                    @error('preco_bilhete') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total de bilhetes *</label>
                    <input type="number" name="total_bilhetes" required min="2" max="10000"
                           value="{{ old('total_bilhetes', '100') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all" />
                    @error('total_bilhetes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data do Sorteio (opcional)</label>
                <input type="datetime-local" name="data_sorteio"
                       value="{{ old('data_sorteio') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all" />
                @error('data_sorteio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm text-amber-800">
                ℹ️ Ao criar a rifa, todos os bilhetes serão gerados automaticamente no banco de dados.
            </div>

            <button type="submit"
                    class="w-full bg-amber-700 hover:bg-amber-800 active:scale-95 text-white
                           font-bold py-3 rounded-xl shadow-lg transition-all text-base">
                🎟️ Criar Rifa
            </button>
        </form>
    </div>
</div>
@endsection
