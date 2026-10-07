@extends('layouts.rifa')

@section('title', 'Rifa da Cesta Mineira')

@section('content')

{{-- CARD DO PRÊMIO --}}
<div class="rounded-2xl overflow-hidden shadow-lg border border-amber-200 bg-white mb-6">

    {{-- Banner do prêmio --}}
    <div class="relative bg-gradient-to-br from-amber-700 via-amber-600 to-yellow-500 p-6 text-white">
        {{-- Padrão de pontos decorativo --}}
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px);
                    background-size: 28px 28px;">
        </div>

        <div class="relative flex flex-col sm:flex-row gap-5 items-center">
            {{-- Ícone da cesta --}}
            <div class="flex-shrink-0 w-32 h-32 rounded-2xl bg-white/20 backdrop-blur-sm
                        flex flex-col items-center justify-center shadow-inner border border-white/30 gap-1">
                <span class="text-5xl emoji-bounce">🧺</span>
                <div class="flex gap-1 text-2xl">
                    <span>🧀</span><span>🍬</span>
                </div>
            </div>

            <div class="text-center sm:text-left">
                <p class="text-amber-100 text-xs font-semibold uppercase tracking-widest mb-1">
                    Prêmio Principal
                </p>
                <h2 class="text-2xl font-bold leading-tight drop-shadow">
                    Cesta de Produtos Mineiros
                </h2>
                <p class="text-amber-100 text-sm mt-2 leading-relaxed max-w-md">
                    Uma seleção especial dos melhores sabores de Minas Gerais,
                    feita com carinho e tradição. 🤎
                </p>

                {{-- Tags dos itens --}}
                <div class="mt-3 flex flex-wrap gap-2 justify-center sm:justify-start">
                    <span class="bg-white/20 border border-white/30 rounded-full px-3 py-1 text-xs font-medium">🧀 Queijo Frescal</span>
                    <span class="bg-white/20 border border-white/30 rounded-full px-3 py-1 text-xs font-medium">🍮 Doce de Leite</span>
                    <span class="bg-white/20 border border-white/30 rounded-full px-3 py-1 text-xs font-medium">🍫 Rapadura</span>
                    <span class="bg-white/20 border border-white/30 rounded-full px-3 py-1 text-xs font-medium">🥛 Requeijão</span>
                    <span class="bg-white/20 border border-white/30 rounded-full px-3 py-1 text-xs font-medium">☕ Café Especial</span>
                    <span class="bg-white/20 border border-white/30 rounded-full px-3 py-1 text-xs font-medium">🍪 Biscoito de Polvilho</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 divide-x divide-amber-100 border-t border-amber-100">
        <div class="px-4 py-3 text-center">
            <p class="text-gray-500 text-xs">Preço / bilhete</p>
            <p class="text-xl font-bold text-amber-700">R$ {{ number_format($rifa->preco_bilhete, 2, ',', '.') }}</p>
        </div>
        <div class="px-4 py-3 text-center">
            <p class="text-gray-500 text-xs">Disponíveis</p>
            <p class="text-xl font-bold text-green-600" id="stat-avail">{{ $totalDisponivel }}</p>
        </div>
        <div class="px-4 py-3 text-center">
            <p class="text-gray-500 text-xs">Sorteio em</p>
            <p class="text-xl font-bold text-amber-700" id="countdown">–</p>
        </div>
    </div>

    {{-- Barra de progresso --}}
    <div class="px-4 pb-4 pt-3 border-t border-amber-100">
        <div class="flex justify-between text-xs text-gray-500 mb-1.5">
            <span>Progresso das vendas</span>
            <span id="pct-label">{{ $percentVendido }}% vendido</span>
        </div>
        <div class="h-3 rounded-full bg-amber-100 overflow-hidden">
            <div class="progress-shimmer h-full rounded-full transition-all duration-700"
                 id="progress-fill"
                 style="width: {{ $percentVendido }}%">
            </div>
        </div>
        <div class="flex justify-between text-xs mt-1 text-gray-400">
            <span>{{ $totalVendido }} vendidos</span>
            <span>{{ $totalBilhetes }} total</span>
        </div>
    </div>
</div>

{{-- GRADE DE BILHETES --}}
<div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-5 mb-6">
    <div class="flex items-start justify-between mb-4 flex-wrap gap-2">
        <div>
            <h3 class="font-bold text-lg text-gray-800">🎟️ Escolha seus bilhetes</h3>
            <p class="text-gray-500 text-sm">Clique para selecionar. Você pode escolher mais de um!</p>
        </div>
        <div class="flex gap-3 text-xs flex-wrap">
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded bg-amber-100 border-2 border-amber-400"></div>
                <span class="text-gray-500">Disponível</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded bg-amber-600 border-2 border-amber-700"></div>
                <span class="text-gray-500">Selecionado</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded bg-orange-200 border-2 border-orange-300"></div>
                <span class="text-gray-500">Reservado</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded bg-gray-200 border-2 border-gray-300"></div>
                <span class="text-gray-500">Vendido</span>
            </div>
        </div>
    </div>

    {{-- Grade gerada pelo JS com os dados do PHP --}}
    <div class="grid grid-cols-10 gap-1.5" id="ticket-grid"></div>

    {{-- Resumo da seleção (aparece ao selecionar) --}}
    <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 hidden" id="selection-summary">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-amber-800 text-sm font-semibold">
                    🎟️ Selecionados: <span id="sel-list" class="font-bold"></span>
                </p>
                <p class="text-amber-700 text-xs">
                    Total: <span id="sel-total" class="font-bold text-base"></span>
                </p>
            </div>
            <button onclick="openModal()"
                    class="bg-amber-700 hover:bg-amber-800 active:scale-95 text-white text-sm font-bold
                           px-5 py-2.5 rounded-xl shadow transition-all hover:scale-105">
                Comprar Agora 🛒
            </button>
        </div>
    </div>
</div>

{{-- GANHADORES ANTERIORES --}}
@if(isset($ganhadores) && $ganhadores->count())
<div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-5 mb-6">
    <h3 class="font-bold text-base text-gray-800 mb-3">🏆 Ganhadores Anteriores</h3>
    <div class="space-y-2">
        @foreach($ganhadores as $g)
        <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-amber-200 flex items-center justify-center text-lg">🏅</div>
                <div>
                    <p class="font-semibold text-sm text-amber-900">{{ $g->comprador_nome }}</p>
                    <p class="text-xs text-amber-600">Bilhete #{{ str_pad($g->numero, 3, '0', STR_PAD_LEFT) }} · {{ $g->updated_at->format('M/Y') }}</p>
                </div>
            </div>
            <span class="text-amber-700 text-lg">🥇</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ===================== MODAL DE COMPRA ===================== --}}
<div id="modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background: rgba(0,0,0,0.55); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-amber-100 overflow-hidden">

        {{-- Cabeçalho do modal --}}
        <div class="bg-gradient-to-r from-amber-700 to-amber-600 text-white p-5">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-lg">🛒 Finalizar Compra</h3>
                <button onclick="closeModal()"
                        class="text-amber-200 hover:text-white text-2xl leading-none font-bold transition-colors">
                    &times;
                </button>
            </div>
            <p class="text-amber-100 text-sm mt-1">Preencha seus dados para reservar os bilhetes</p>
        </div>

        {{-- Formulário --}}
        <form method="POST" action="{{ route('rifa.comprar') }}" id="form-compra">
            @csrf
            <input type="hidden" name="rifa_id" value="{{ $rifa->id }}" />
            <input type="hidden" name="bilhetes" id="input-bilhetes" />

            <div class="p-5 space-y-4" id="form-section">

                {{-- Resumo --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm">
                    <p class="text-amber-800 font-semibold">Resumo do pedido</p>
                    <p class="text-amber-700 mt-1">Bilhetes: <span class="font-bold" id="modal-tickets"></span></p>
                    <p class="text-amber-700">Total: <span class="font-bold text-base" id="modal-total"></span></p>
                </div>

                {{-- Dados pessoais --}}
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo *</label>
                        <input type="text" name="nome" required
                               placeholder="Ex: Maria Aparecida Silva"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp / Telefone *</label>
                        <input type="tel" name="telefone" required
                               placeholder="(31) 9 9999-9999"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail (opcional)</label>
                        <input type="email" name="email"
                               placeholder="seu@email.com"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all" />
                    </div>
                </div>

                {{-- Forma de pagamento --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Forma de pagamento</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="pagamento" value="pix" class="sr-only peer" checked>
                            <div class="border-2 border-gray-200 peer-checked:border-amber-600 peer-checked:bg-amber-50
                                        rounded-xl p-2 text-center transition-all">
                                <div class="text-xl">💚</div>
                                <div class="text-xs font-semibold mt-1">Pix</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="pagamento" value="cartao" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-amber-600 peer-checked:bg-amber-50
                                        rounded-xl p-2 text-center transition-all">
                                <div class="text-xl">💳</div>
                                <div class="text-xs font-semibold mt-1">Cartão</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="pagamento" value="boleto" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-amber-600 peer-checked:bg-amber-50
                                        rounded-xl p-2 text-center transition-all">
                                <div class="text-xl">📄</div>
                                <div class="text-xs font-semibold mt-1">Boleto</div>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-amber-700 hover:bg-amber-800 active:scale-95
                               text-white font-bold py-3 rounded-xl shadow-lg transition-all text-base">
                    ✅ Confirmar Compra
                </button>
                <p class="text-center text-xs text-gray-400">🔒 Seus dados estão seguros e protegidos</p>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Dados vindos do PHP (bilhetes vendidos)
    const PRICE      = {{ $rifa->preco_bilhete }};
    const TOTAL      = {{ $totalBilhetes }};
    const soldSet    = new Set({{ Js::from($vendidos) }});    // pago → cinza
    const pendSet    = new Set({{ Js::from($pendentes) }});   // pendente → laranja
    const selected   = new Set();

    // ---- Grade de bilhetes ----
    function buildGrid() {
        const grid = document.getElementById('ticket-grid');
        grid.innerHTML = '';

        for (let i = 1; i <= TOTAL; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = String(i).padStart(2, '0');
            btn.dataset.num = i;

            btn.className = 'ticket-btn aspect-square rounded-lg text-xs font-bold flex items-center justify-center';

            if (soldSet.has(i)) {
                // Vendido/confirmado → cinza
                btn.className += ' bg-gray-200 text-gray-400 cursor-not-allowed border border-gray-300';
                btn.disabled = true;
                btn.title = 'Bilhete já vendido';
            } else if (pendSet.has(i)) {
                // Reservado aguardando pagamento → laranja
                btn.className += ' bg-orange-200 text-orange-600 cursor-not-allowed border border-orange-300';
                btn.disabled = true;
                btn.title = 'Bilhete reservado — aguardando pagamento';
            } else if (selected.has(i)) {
                btn.className += ' selected bg-amber-600 text-white border-2 border-amber-700 shadow-md';
            } else {
                btn.className += ' bg-amber-100 text-amber-800 border-2 border-amber-300 hover:bg-amber-200';
            }

            btn.addEventListener('click', () => toggle(i));
            grid.appendChild(btn);
        }
    }

    function toggle(num) {
        if (soldSet.has(num)) return;
        selected.has(num) ? selected.delete(num) : selected.add(num);
        buildGrid();
        updateSummary();
    }

    function updateSummary() {
        const summary = document.getElementById('selection-summary');
        if (selected.size === 0) { summary.classList.add('hidden'); return; }

        summary.classList.remove('hidden');
        const sorted = [...selected].sort((a, b) => a - b);
        document.getElementById('sel-list').textContent  = sorted.map(n => '#' + String(n).padStart(2,'0')).join(', ');
        document.getElementById('sel-total').textContent = 'R$ ' + (selected.size * PRICE).toFixed(2).replace('.', ',');
    }

    // ---- Modal ----
    function openModal() {
        if (selected.size === 0) return;
        const sorted = [...selected].sort((a, b) => a - b);
        document.getElementById('modal-tickets').textContent  = sorted.map(n => '#' + String(n).padStart(2,'0')).join(', ');
        document.getElementById('modal-total').textContent    = 'R$ ' + (selected.size * PRICE).toFixed(2).replace('.', ',');
        document.getElementById('input-bilhetes').value       = sorted.join(',');
        document.getElementById('modal').style.display        = 'flex';
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }

    // Fechar modal ao clicar fora
    document.getElementById('modal').addEventListener('click', e => {
        if (e.target === document.getElementById('modal')) closeModal();
    });

    // ---- Countdown ----
    (function () {
        const target = new Date('{{ $dataSorteio ?? '2026-09-21T19:00:00' }}');
        const el     = document.getElementById('countdown');

        function tick() {
            const diff = target - new Date();
            if (diff <= 0) { el.textContent = 'Hoje! 🎉'; return; }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000)  / 60000);
            el.textContent = d > 0 ? `${d}d ${h}h` : `${h}h ${m}m`;
        }
        tick();
        setInterval(tick, 60000);
    })();

    buildGrid();
</script>
@endpush
