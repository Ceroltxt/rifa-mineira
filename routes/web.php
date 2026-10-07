<?php

use App\Http\Controllers\RifaController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// ── Rotas públicas da Rifa ───────────────────────────────────────────────────
Route::get('/rifa', [RifaController::class, 'index'])->name('rifa.index');
Route::post('/rifa/comprar', [RifaController::class, 'comprar'])->name('rifa.comprar');
Route::get('/rifa/confirmacao', [RifaController::class, 'confirmacao'])->name('rifa.confirmacao');

// ── Rotas Admin da Rifa ──────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('rifas', [\App\Http\Controllers\Admin\AdminRifaController::class, 'index'])->name('rifa.index');
    Route::get('rifas/criar', [\App\Http\Controllers\Admin\AdminRifaController::class, 'create'])->name('rifa.create');
    Route::post('rifas', [\App\Http\Controllers\Admin\AdminRifaController::class, 'store'])->name('rifa.store');
    Route::get('rifas/{rifa}', [\App\Http\Controllers\Admin\AdminRifaController::class, 'show'])->name('rifa.show');
    Route::post('rifas/{rifa}/sortear', [\App\Http\Controllers\Admin\AdminRifaController::class, 'sortear'])->name('rifa.sortear');
    Route::post('rifas/{rifa}/encerrar', [\App\Http\Controllers\Admin\AdminRifaController::class, 'encerrar'])->name('rifa.encerrar');
    Route::post('rifas/{rifa}/compras/{compra}/validar', [\App\Http\Controllers\Admin\AdminRifaController::class, 'validar'])->name('rifa.compra.validar');
    Route::post('rifas/{rifa}/compras/{compra}/rejeitar', [\App\Http\Controllers\Admin\AdminRifaController::class, 'rejeitar'])->name('rifa.compra.rejeitar');
});

// ── Rota padrão do Laravel / Inertia ────────────────────────────────────────
Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
