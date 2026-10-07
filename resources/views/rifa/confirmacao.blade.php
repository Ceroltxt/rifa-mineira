@extends('layouts.rifa')

@section('title', 'Pedido Realizado!')

@section('content')
<div class="max-w-md mx-auto py-4">

    <div class="bg-white rounded-2xl border border-amber-200 shadow-lg overflow-hidden">

        {{-- Header verde --}}
        <div class="bg-gradient-to-r from-amber-700 to-amber-600 p-6 text-white text-center">
            <div class="text-5xl mb-2">🎟️</div>
            <h2 class="text-xl font-bold">Bilhetes Reservados!</h2>
            <p class="text-amber-100 text-sm mt-1">Agora é só pagar e aguardar a confirmação</p>
        </div>

        <div class="p-5 space-y-5">

            {{-- Resumo --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm space-y-2">
                <p class="font-semibold text-amber-800 text-base">Resumo do pedido</p>
                <div class="flex justify-between">
                    <span class="text-gray-500">Participante</span>
                    <span class="font-semibold text-gray-800">{{ session('compra_nome') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Bilhetes</span>
                    <span class="font-semibold text-gray-800">{{ session('compra_bilhetes') }}</span>
                </div>
                <div class="flex justify-between items-center border-t border-amber-200 pt-2 mt-2">
                    <span class="text-gray-500">Total a pagar</span>
                    <span class="font-bold text-xl text-amber-700">R$ {{ session('compra_total') }}</span>
                </div>
            </div>

            {{-- Instruções de pagamento --}}
            <div class="border-2 border-green-400 rounded-xl overflow-hidden">
                <div class="bg-green-500 text-white px-4 py-2.5 flex items-center gap-2 font-bold">
                    💚 Como pagar via Pix
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <p class="text-gray-600">
                        Faça um Pix no valor de
                        <strong class="text-gray-800">R$ {{ session('compra_total') }}</strong>
                        para a chave abaixo:
                    </p>

                    {{-- Chave Pix --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Chave Pix (Telefone)</p>
                            <p class="font-bold text-gray-800 text-base tracking-wide" id="pix-key">
                                {{ env('RIFA_PIX_KEY', '(31) 9 9999-9999') }}
                            </p>
                        </div>
                        <button onclick="copiarPix()"
                                class="flex-shrink-0 bg-green-500 hover:bg-green-600 text-white text-xs font-bold
                                       px-3 py-2 rounded-lg transition-all active:scale-95"
                                id="btn-copiar">
                            📋 Copiar
                        </button>
                    </div>

                    <p class="text-gray-500 text-xs">
                        Nome favorecido: <strong class="text-gray-700">{{ env('RIFA_NOME', 'Rifa Mineira') }}</strong>
                    </p>
                </div>
            </div>

            {{-- Instrução de envio no WhatsApp --}}
            <div class="border-2 border-green-300 bg-green-50 rounded-xl p-4 text-sm">
                <p class="font-bold text-green-800 mb-2">📲 Após pagar:</p>
                <ol class="text-green-700 space-y-1.5 list-decimal list-inside">
                    <li>Tire um <strong>print do comprovante</strong> do Pix</li>
                    <li>Mande via WhatsApp para <strong>{{ env('RIFA_WHATSAPP', '(31) 9 9999-9999') }}</strong></li>
                    <li>
                        Informe seus bilhetes: <strong>{{ session('compra_bilhetes') }}</strong>
                    </li>
                </ol>
                <a href="https://wa.me/{{ preg_replace('/\D/', '', env('RIFA_WHATSAPP', '31999999999')) }}?text={{ urlencode('Olá! Realizei o pagamento da rifa. Meus bilhetes são: ' . session('compra_bilhetes') . '. Nome: ' . session('compra_nome')) }}"
                   target="_blank"
                   class="mt-3 w-full flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600
                          text-white font-bold py-3 rounded-xl transition-all hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Enviar comprovante no WhatsApp
                </a>
            </div>

            {{-- Aviso --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700 text-center">
                ⏳ Seus bilhetes ficam <strong>reservados por 24h</strong>. Após o envio do comprovante,
                confirmamos em até <strong>2h</strong>.
            </div>

        </div>

        <div class="px-5 pb-5">
            <a href="{{ route('rifa.index') }}"
               class="block text-center text-amber-700 text-sm hover:underline">
                ← Voltar à rifa
            </a>
        </div>

    </div>

    <div class="mt-6 text-center text-2xl flex justify-center gap-2 opacity-50">
        <span>🧀</span><span>🍮</span><span>🍫</span><span>🥛</span><span>☕</span><span>🍪</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copiarPix() {
    const key = document.getElementById('pix-key').textContent.trim();
    const btn = document.getElementById('btn-copiar');
    navigator.clipboard.writeText(key).then(() => {
        btn.textContent = '✅ Copiado!';
        btn.classList.replace('bg-green-500', 'bg-gray-400');
        setTimeout(() => {
            btn.textContent = '📋 Copiar';
            btn.classList.replace('bg-gray-400', 'bg-green-500');
        }, 2000);
    });
}
</script>
@endpush
