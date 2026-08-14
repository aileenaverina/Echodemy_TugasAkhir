<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Echodemy') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-cream text-ink antialiased">

    <x-header-user />

    <div class="max-w-7xl mx-auto lg:flex">
        <div id="sidebar-overlay" onclick="closeSidebar()"
             class="hidden fixed inset-0 bg-ink/30 z-30 lg:hidden"></div>

        <aside id="sidebar"
               class="fixed z-40 inset-y-0 left-0 w-64 bg-white border-r border-ink/10 px-4 py-6 -translate-x-full transition-transform duration-200 lg:translate-x-0 lg:static lg:block lg:w-56 lg:shrink-0">
            @switch(auth()->user()->role?->value)
                @case('sekolah') <x-sidebar-sekolah /> @break
                @case('admin') <x-sidebar-admin /> @break
                @case('guru') <x-sidebar-guru /> @break
                @case('siswa') <x-sidebar-siswa /> @break
            @endswitch
        </aside>

        <div class="flex-1 min-w-0 px-4 sm:px-6 lg:px-8 py-6">
            <button onclick="openSidebar()" class="lg:hidden mb-4 flex items-center gap-2 text-sm font-medium text-ink/70">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                Menu
            </button>

            @isset($header)
                <div class="mb-6">{{ $header }}</div>
            @endisset

            {{ $slot }}
        </div>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
    </script>
</body>
</html>