<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Log Aktivitas</p>
        <h1 class="font-display text-2xl font-semibold">Riwayat Aktivitas Siswa</h1>
    </x-slot>

    <form method="GET" id="search-form" class="mb-5">
        <div class="relative max-w-md">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-hitam2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
            </svg>
            <input type="text" name="search" id="search-input" value="{{ $search }}" placeholder="Cari Waktu, Oleh, atau Deskripsi..."
                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-putih focus:border-coral focus:ring-coral text-sm">
        </div>
    </form>

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
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 text-hitam2">{{ $log->created_at->format('d/m/y H.i.s') }}</td>
                        <td class="px-5 py-4 flex items-center gap-2">
                            <img src="{{ $log->user?->photoUrl() }}" alt="" class="w-6 h-6 rounded-full object-cover">
                            <span class="font-medium">{{ $log->user?->nama_lengkap ?? 'Sistem' }}</span>
                        </td>
                        <td class="px-5 py-4">{{ $log->deskripsi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-10 text-center text-hitam2">
                            @if ($search)
                                Tidak ada aktivitas yang cocok dengan "{{ $search }}".
                            @else
                                Belum ada aktivitas siswa tercatat.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>

    <script>
    $(function () {
        let searchTimeout;
        $('#search-input').on('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () { $('#search-form').submit(); }, 500);
        });
    });
    </script>
</x-app-layout>