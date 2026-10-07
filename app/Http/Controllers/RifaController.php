<?php

namespace App\Http\Controllers;

use App\Models\Bilhete;
use App\Models\Compra;
use App\Models\Rifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RifaController extends Controller
{
    /**
     * Página principal — mostra a rifa ativa.
     */
    public function index()
    {
        $rifa = Rifa::where('status', 'ativa')->latest()->first();

        if (! $rifa) {
            return view('rifa.sem-rifa');
        }

        // Bilhetes confirmados (pago) — aparecem cinza na grade
        $vendidos = $rifa->bilhetes()
            ->where('status', 'pago')
            ->pluck('numero')
            ->toArray();

        // Bilhetes aguardando validação — aparecem laranja na grade
        $pendentes = $rifa->bilhetes()
            ->where('status', 'pendente')
            ->pluck('numero')
            ->toArray();

        $ganhadores = Rifa::where('status', 'sorteada')
            ->with('ganhador')
            ->latest()
            ->take(5)
            ->get()
            ->filter(fn ($r) => $r->ganhador !== null);

        return view('rifa.index', [
            'rifa'            => $rifa,
            'totalBilhetes'   => $rifa->total_bilhetes,
            'totalVendido'    => count($vendidos),
            'totalDisponivel' => $rifa->total_bilhetes - count($vendidos) - count($pendentes),
            'percentVendido'  => $rifa->percentVendido(),
            'vendidos'        => $vendidos,
            'pendentes'       => $pendentes,
            'dataSorteio'     => $rifa->data_sorteio?->toIso8601String() ?? '',
            'ganhadores'      => $ganhadores,
        ]);
    }

    /**
     * Processa a compra dos bilhetes selecionados.
     */
    public function comprar(Request $request)
    {
        $request->validate([
            'rifa_id'   => ['required', 'exists:rifas,id'],
            'nome'      => ['required', 'string', 'max:255'],
            'telefone'  => ['required', 'string', 'max:30'],
            'email'     => ['nullable', 'email', 'max:255'],
            'pagamento' => ['required', 'in:pix,cartao,boleto'],
            'bilhetes'  => ['required', 'string'],
        ]);

        $rifa = Rifa::findOrFail($request->rifa_id);

        if (! $rifa->isAtiva()) {
            return back()->with('error', 'Esta rifa não está mais ativa.');
        }

        $numeros = array_unique(array_filter(
            array_map('intval', explode(',', $request->bilhetes)),
            fn ($n) => $n >= 1 && $n <= $rifa->total_bilhetes
        ));

        if (empty($numeros)) {
            return back()->with('error', 'Nenhum bilhete válido selecionado.');
        }

        // Bloqueia bilhetes já pagos OU pendentes (reservados por outro comprador)
        $ocupados = $rifa->bilhetes()
            ->whereIn('numero', $numeros)
            ->whereIn('status', ['pago', 'pendente'])
            ->pluck('numero')
            ->toArray();

        if (! empty($ocupados)) {
            $lista = implode(', ', array_map(fn ($n) => '#' . str_pad($n, 2, '0', STR_PAD_LEFT), $ocupados));
            return back()->with('error', "Os bilhetes {$lista} já estão reservados. Por favor escolha outros.");
        }

        $valorTotal = count($numeros) * $rifa->preco_bilhete;

        DB::transaction(function () use ($rifa, $request, $numeros, $valorTotal) {
            $compra = Compra::create([
                'rifa_id'            => $rifa->id,
                'comprador_nome'     => $request->nome,
                'comprador_email'    => $request->email,
                'comprador_telefone' => $request->telefone,
                'total_bilhetes'     => count($numeros),
                'valor_total'        => $valorTotal,
                'forma_pagamento'    => $request->pagamento,
                'status'             => 'pendente', // aguarda validação do admin
            ]);

            // Bilhetes ficam como PENDENTE até admin validar o pagamento
            foreach ($numeros as $numero) {
                Bilhete::updateOrCreate(
                    ['rifa_id' => $rifa->id, 'numero' => $numero],
                    [
                        'compra_id'          => $compra->id,
                        'status'             => 'pendente',
                        'comprador_nome'     => $request->nome,
                        'comprador_email'    => $request->email,
                        'comprador_telefone' => $request->telefone,
                    ]
                );
            }

            session([
                'compra_id'       => $compra->id,
                'compra_nome'     => $request->nome,
                'compra_bilhetes' => implode(', ', array_map(fn ($n) => '#' . str_pad($n, 2, '0', STR_PAD_LEFT), $numeros)),
                'compra_total'    => number_format($valorTotal, 2, ',', '.'),
                'compra_pagto'    => $request->pagamento,
                'compra_telefone' => $request->telefone,
            ]);
        });

        return redirect()->route('rifa.confirmacao');
    }

    /**
     * Página de confirmação após a compra.
     */
    public function confirmacao()
    {
        if (! session()->has('compra_nome')) {
            return redirect()->route('rifa.index');
        }

        return view('rifa.confirmacao');
    }
}
