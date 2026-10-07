<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bilhete;
use App\Models\Compra;
use App\Models\Rifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRifaController extends Controller
{
    /** Dashboard admin — lista todas as rifas */
    public function index()
    {
        $rifas = Rifa::withCount([
            'bilhetes as vendidos_count' => fn ($q) => $q->where('status', 'pago'),
        ])->latest()->get();

        return view('admin.rifa.index', compact('rifas'));
    }

    /** Formulário de criação */
    public function create()
    {
        return view('admin.rifa.create');
    }

    /** Salva nova rifa e gera os bilhetes */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'          => ['required', 'string', 'max:255'],
            'descricao'       => ['nullable', 'string'],
            'preco_bilhete'   => ['required', 'numeric', 'min:0.01'],
            'total_bilhetes'  => ['required', 'integer', 'min:2', 'max:10000'],
            'data_sorteio'    => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($data) {
            $rifa = Rifa::create($data);

            // Gera todos os bilhetes de uma vez (insert em lote)
            $bilhetes = [];
            $now = now();
            for ($i = 1; $i <= $rifa->total_bilhetes; $i++) {
                $bilhetes[] = [
                    'rifa_id'    => $rifa->id,
                    'numero'     => $i,
                    'status'     => 'disponivel',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            // Insere em chunks de 500 para evitar limite de SQL
            foreach (array_chunk($bilhetes, 500) as $chunk) {
                Bilhete::insert($chunk);
            }
        });

        return redirect()->route('admin.rifa.index')->with('success', 'Rifa criada com sucesso!');
    }

    /** Detalhe da rifa: lista de compras + botão sorteio */
    public function show(Rifa $rifa)
    {
        $compras = $rifa->compras()->with('bilhetes')->latest()->paginate(20);
        $vendidos = $rifa->bilhetes()->where('status', 'pago')->count();

        return view('admin.rifa.show', compact('rifa', 'compras', 'vendidos'));
    }

    /** Valida o pagamento de uma compra — marca bilhetes como pago */
    public function validar(Rifa $rifa, Compra $compra)
    {
        if ($compra->rifa_id !== $rifa->id) {
            return back()->with('error', 'Compra não pertence a esta rifa.');
        }

        DB::transaction(function () use ($compra) {
            $compra->update(['status' => 'pago']);
            $compra->bilhetes()->update(['status' => 'pago']);
        });

        return back()->with('success', "✅ Pagamento de {$compra->comprador_nome} validado! Bilhetes confirmados.");
    }

    /** Rejeita o pagamento — libera os bilhetes de volta */
    public function rejeitar(Rifa $rifa, Compra $compra)
    {
        if ($compra->rifa_id !== $rifa->id) {
            return back()->with('error', 'Compra não pertence a esta rifa.');
        }

        DB::transaction(function () use ($compra) {
            $compra->update(['status' => 'cancelado']);
            $compra->bilhetes()->update([
                'status'             => 'disponivel',
                'compra_id'          => null,
                'comprador_nome'     => null,
                'comprador_email'    => null,
                'comprador_telefone' => null,
            ]);
        });

        return back()->with('success', "❌ Compra de {$compra->comprador_nome} rejeitada. Bilhetes liberados.");
    }

    /** Realiza o sorteio aleatório */
    public function sortear(Rifa $rifa)
    {
        if ($rifa->status !== 'ativa') {
            return back()->with('error', 'Esta rifa não está ativa.');
        }

        $bilheteGanhador = $rifa->bilhetes()
            ->where('status', 'pago')
            ->inRandomOrder()
            ->first();

        if (! $bilheteGanhador) {
            return back()->with('error', 'Nenhum bilhete vendido ainda — não é possível sortear.');
        }

        $rifa->update([
            'status'              => 'sorteada',
            'ganhador_bilhete_id' => $bilheteGanhador->id,
        ]);

        return redirect()->route('admin.rifa.show', $rifa)
            ->with('success', "🎉 Ganhador: {$bilheteGanhador->comprador_nome} — Bilhete #{$bilheteGanhador->numero}");
    }

    /** Encerra rifa sem sorteio */
    public function encerrar(Rifa $rifa)
    {
        $rifa->update(['status' => 'encerrada']);
        return back()->with('success', 'Rifa encerrada.');
    }
}
