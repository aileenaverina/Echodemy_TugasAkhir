<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Log Sistem</p>
        <h1 class="font-display text-2xl font-semibold">Riwayat Aktivitas Platform</h1>
    </x-slot>

    <!-- Search + Filter -->
    <form method="GET" action="{{ route('admin.log.index') }}" id="filter-form" class="flex flex-wrap items-center gap-3 mb-5">
        <input type="hidden" name="filter" id="filter-input" value="{{ $filter }}">

        <div class="relative flex-1 min-w-[240px]">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-hitam2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
            </svg>
            <input type="text" name="search" id="search-input" value="{{ $search }}" placeholder="Cari Waktu, Oleh, atau Deskripsi..."
                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-putih focus:border-coral focus:ring-coral text-sm">
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @php
                $tabs = [
                    'semua' => 'Semua',
                    'admin' => 'Admin',
                    'sekolah' => 'Sekolah',
                    'guru' => 'Guru',
                    'siswa' => 'Siswa',
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <button type="button" data-filter="{{ $key }}"
                    class="filter-tab px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === $key ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    {{ $label }} &middot; {{ $counts[$key] }}
                </button>
            @endforeach
        </div>
    </form>

    <!-- Table -->
    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Waktu</th>
                    <th class="px-5 py-3 font-semibold">Oleh</th>
                    <th class="px-5 py-3 font-semibold">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    @php
                        $role = $log->user?->role?->value;
                        $badgeClass = match ($role) {
                            'admin' => 'bg-amber/60 text-amber',
                            'sekolah' => 'bg-periwinkle/60 text-periwinkle',
                            'guru' => 'bg-teal/60 text-teal',
                            'siswa' => 'bg-coral/60 text-coral',
                            default => 'bg-cream2 text-hitam2',
                        };
                        $roleLabel = match ($role) {
                            'admin' => 'Admin',
                            'sekolah' => 'Sekolah',
                            'guru' => 'Guru',
                            'siswa' => 'Siswa',
                            'orang_tua' => 'Orang Tua',
                            default => '-',
                        };
                    @endphp
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 text-hitam2">{{ $log->created_at->format('d/m/y H.i.s') }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $roleLabel }}</span>
                        </td>
                        <td class="px-5 py-4">
                            {{ $log->user?->displayName() ?? 'Sistem' }} {{ $log->deskripsi }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-hitam2">Belum ada aktivitas tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

    <script>
    $(function () {
        $('.filter-tab').on('click', function () {
            $('#filter-input').val($(this).data('filter'));
            $('#filter-form').submit();
        });

        let searchTimeout;
        $('#search-input').on('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                $('#filter-form').submit();
            }, 500);
        });
    });
    </script>
</x-app-layout>