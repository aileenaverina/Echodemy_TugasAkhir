<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.rapor.kelas', $kelas) }}" class="hover:underline">Nilai</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">Nilai {{ $siswa->nama_lengkap }}</h1>
    </x-slot>

    @if ($mataPelajaranKelasList->isEmpty())
        <div class="bg-putih rounded-2xl shadow-sm p-8 text-center text-hitam2">
            Belum ada mata pelajaran di kelas ini.
        </div>
    @else
        <div class="flex items-center gap-2 mb-5 flex-wrap">
            @foreach ($mataPelajaranKelasList as $mpk)
                <a href="{{ route('guru.rapor.siswa', [$kelas, $siswa, 'mapel' => $mpk->id]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $activeMpk?->id === $mpk->id ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    {{ $mpk->mataPelajaran->nama }}
                </a>
            @endforeach
        </div>

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
                            <td class="px-5 py-4 text-right">{{ $item['nilai'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-8 text-center text-hitam2">Belum ada tugas atau kuis di mata pelajaran ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->isNotEmpty())
            <div class="flex items-center justify-between px-5 py-4">
                <p class="font-display font-semibold text-teal">HASIL AKHIR</p>
                <p class="font-display font-semibold text-lg">{{ $hasilAkhir ?? '-' }}</p>
            </div>
        @endif
    @endif
</x-app-layout>