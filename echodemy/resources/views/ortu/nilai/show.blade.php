<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('ortu.nilai.index') }}" class="hover:underline">Nilai dan Evaluasi</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">{{ $mataPelajaranKelas->mataPelajaran->nama }}</h1>
    </x-slot>

    <div class="bg-cream2 rounded-2xl p-6 max-w-2xl">
        <div class="bg-putih rounded-2xl shadow-sm p-5 mb-4 flex items-center gap-10">
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Siswa</p>
                <p class="font-medium">{{ $jumlahSiswa }} Siswa</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Pengajar</p>
                <p class="font-medium">{{ $mataPelajaranKelas->gurus->pluck('nama_lengkap')->join(', ') }}</p>
            </div>
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
            <div class="flex items-center justify-between px-1 py-4">
                <p class="font-display font-semibold text-teal">Hasil Akhir</p>
                <p class="font-display font-semibold text-lg">{{ $hasilAkhir ?? '-' }}</p>
            </div>
        @endif
    </div>
</x-app-layout>