<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rifa Mineira') – Sabores de Minas Gerais</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <style>
        @keyframes bounce-gentle {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-5px); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position:  200% 0; }
        }
        @keyframes pop {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.22); }
            100% { transform: scale(1); }
        }
        .emoji-bounce { display: inline-block; animation: bounce-gentle 2.2s ease-in-out infinite; }
        .progress-shimmer {
            background: linear-gradient(90deg, #d97706, #92400e, #d97706);
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
        }
        .ticket-btn { transition: all .17s cubic-bezier(.4,0,.2,1); }
        .ticket-btn:not(:disabled):hover { transform: scale(1.15); box-shadow: 0 4px 14px rgba(180,120,40,.28); }
        .ticket-btn.selected { animation: pop .22s ease; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #d97706; border-radius: 3px; }
    </style>

    @stack('styles')
</head>
<body class="bg-amber-50 text-gray-800 font-sans antialiased min-h-screen">

    <!-- HEADER -->
    <header class="bg-amber-800 text-amber-50 shadow-lg sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('rifa.index') }}" class="flex items-center gap-3 hover:opacity-90 transition-opacity">
                <span class="text-3xl emoji-bounce">🧺</span>
                <div>
                    <h1 class="text-lg font-bold tracking-tight leading-tight">Rifa Mineira</h1>
                    <p class="text-amber-200 text-xs">Sabores de Minas Gerais</p>
                </div>
            </a>
            <div class="flex items-center gap-2">
                <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">🟢 Ativa</span>
            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="max-w-4xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl px-4 py-3 flex items-center gap-2">
                ✅ {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-4xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-800 rounded-xl px-4 py-3 flex items-center gap-2">
                ❌ {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="max-w-4xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="mt-12 bg-amber-800 text-amber-200 text-center py-5 text-sm">
        <p>🧺 Rifa Mineira · Feito com ❤️ e muito queijo</p>
        <p class="text-amber-400 text-xs mt-1">Os participantes serão contatados pelo WhatsApp informado na compra.</p>
    </footer>

    @stack('scripts')
</body>
</html>
