<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Nilai</p>
                <h1 class="font-display text-2xl font-semibold">Nilai {{ auth()->user()->nama_lengkap }}</h1>
            </div>
            <div class="relative group">
                <button type="button" disabled
                    class="bg-cream2 text-hitam2 text-sm font-medium rounded-lg px-4 py-2.5 cursor-not-allowed">
                    Lihat Raport
                </button>
                <span class="pointer-events-none absolute -bottom-9 right-0 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                    Segera hadir
                </span>
            </div>
        </div>
    </x-slot>

    @if (! $kelas)
        <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">
            Kamu belum terdaftar di kelas manapun.
        </div>
    @elseif ($mataPelajaranKelasList->isEmpty())
        <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">
            Belum ada mata pelajaran di kelasmu.
        </div>
    @else
        <!-- Tab mata pelajaran -->
        <div class="flex items-center gap-2 mb-5 flex-wrap">
            @foreach ($mataPelajaranKelasList as $mpk)
                @php $isActive = $activeMpk?->id === $mpk->id; @endphp
                <a href="{{ route('siswa.nilai.index', ['mapel' => $mpk->id]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $isActive ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    <span class="inline-block w-2 h-2 rounded-full mr-1.5 align-middle" style="background-color: {{ $mpk->mataPelajaran->warna }}"></span>
                    {{ $mpk->mataPelajaran->nama }}
                </a>
            @endforeach
        </div>

        <!-- Tabel nilai -->
        <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                        <th class="px-5 py-3 font-semibold">Nama</th>
                        <th class="px-5 py-3 font-semibold text-right">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="border-b border-border last:border-0">
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $item['nama'] }}</p>
                                <p class="text-xs text-hitam2">{{ $item['section'] }}</p>
                            </td>
                            <td class="px-5 py-4 text-right font-medium">
                                {{ $item['nilai'] ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-10 text-center text-hitam2">Belum ada tugas atau kuis di mata pelajaran ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Hasil akhir -->
        @if ($items->isNotEmpty())
            <div class="bg-teal/10 rounded-2xl mt-4 px-5 py-4 flex items-center justify-between">
                <p class="font-display font-semibold text-teal">Hasil Akhir</p>
                <p class="font-display font-semibold text-2xl">{{ $hasilAkhir ?? '-' }}</p>
            </div>
        @endif
    @endif
</x-app-layout>