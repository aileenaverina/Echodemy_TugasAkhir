<!DOCTYPE html>
<html lang="id" >

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Echodemy') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-mark.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-body bg-cream text-ink antialiased">

    <div class="sticky top-0 z-50">
        <x-header-user />
    </div>

    <div class=" lg:flex items-start">
        <div id="sidebar-overlay" class="hidden fixed inset-0 bg-ink/30 z-30 lg:hidden"></div>

        <aside id="sidebar"
            class="fixed z-40 inset-y-0 left-0 w-64 bg-putih border-r border-border px-4 py-6 -translate-x-full transition-transform duration-200 overflow-y-auto lg:translate-x-0 lg:sticky lg:top-16 lg:h-[calc(100vh-4rem)] lg:w-56 lg:shrink-0">
            @switch(auth()->user()->role?->value)
                @case('sekolah')
                    <x-sidebar-sekolah />
                @break

                @case('admin')
                    <x-sidebar-admin />
                @break

                @case('guru')
                    <x-sidebar-guru />
                @break

                @case('siswa')
                    <x-sidebar-siswa />
                @break
            @endswitch
        </aside>

        <div class="flex-1 min-w-0 px-4 sm:px-6 lg:px-8 py-6">
            <button id="sidebar-open-btn"
                class="lg:hidden mb-4 flex items-center gap-2 text-sm font-medium text-hitam2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Menu
            </button>

            @isset($header)
                <div class="mb-6">{{ $header }}</div>
            @endisset

            {{ $slot }}
        </div>
    </div>

    <script>
        $(function() {
            $('#sidebar-open-btn').on('click', function() {
                $('#sidebar').removeClass('-translate-x-full');
                $('#sidebar-overlay').removeClass('hidden');
            });

            $('#sidebar-overlay').on('click', function() {
                $('#sidebar').addClass('-translate-x-full');
                $('#sidebar-overlay').addClass('hidden');
            });
        });
    </script>
</body>

</html>
