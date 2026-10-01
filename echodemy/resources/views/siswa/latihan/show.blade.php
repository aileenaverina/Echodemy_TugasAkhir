<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Kelas Saya</p>
        <h1 class="font-display text-2xl font-semibold">{{ $latihan->judul }}</h1>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
    @endif

    @if ($latihan->batas_waktu)
        <p class="text-sm font-semibold text-coral mb-4">Deadline: {{ $latihan->batas_waktu->translatedFormat('l, d M Y H:i') }}</p>
    @endif

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="bg-putih rounded-2xl shadow-sm p-5 flex items-center gap-10 flex-1">
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Waktu</p>
                <p class="font-display font-semibold text-lg">
                    @if ($latihan->waktu_kerja >= 60 && $latihan->waktu_kerja % 60 === 0)
                        {{ $latihan->waktu_kerja / 60 }} Jam
                    @else
                        {{ $latihan->waktu_kerja }} Menit
                    @endif
                </p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Sisa Percobaan</p>
                <p class="font-display font-semibold text-lg">{{ $sisaPercobaan }}X</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Jumlah Nomor</p>
                <p class="font-display font-semibold text-lg">{{ $latihan->detailLatihans()->count() }}</p>
            </div>
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5 relative">
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Nilai</p>
            <p class="font-display font-semibold text-lg">{{ $nilaiTertinggi ?? '-' }}/100</p>
            <p class="text-[10px] text-hitam2 absolute top-5 right-5 text-right max-w-[110px]">*Diambil berdasarkan nilai tertinggi</p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        @if ($submissionAktif)
            <form method="GET" action="{{ route('siswa.latihan.kerjakan', [$latihan, $submissionAktif]) }}">
                <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">
                    Lanjutkan Mengerjakan
                </button>
            </form>
        @elseif ($sudahPernahKerja)
            <a href="{{ route('siswa.latihan.hasil', [$latihan, $submissionTerakhir]) }}"
                class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">
                Lihat
            </a>
            @if ($sisaPercobaan > 0)
                <form method="POST" action="{{ route('siswa.latihan.mulai', $latihan) }}">
                    @csrf
                    <button type="submit" class="bg-putih border border-border text-ink text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-cream2">
                        Coba Lagi
                    </button>
                </form>
            @endif
        @else
            <form method="POST" action="{{ route('siswa.latihan.mulai', $latihan) }}">
                @csrf
                <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">
                    Kerjakan
                </button>
            </form>
        @endif
    </div>
</x-app-layout>