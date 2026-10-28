<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.mata-pelajaran-kelas.show', $mataPelajaranKelas) }}" class="hover:underline">{{ $mataPelajaranKelas->mataPelajaran->nama }}</a>
            &middot; Nilai dan Evaluasi
        </p>
        <h1 class="font-display text-2xl font-semibold">Rekap Nilai {{ $mataPelajaranKelas->kelas->nama }}</h1>
    </x-slot>

    @include('partials.semester-tabs')

    <div class="bg-putih rounded-2xl shadow-sm p-5 mb-6 flex flex-wrap items-center gap-10">
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Siswa</p>
            <p class="font-medium">{{ $rekap['jumlah_siswa'] }} Siswa</p>
        </div>
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Pengajar</p>
            <p class="font-medium">{{ $mataPelajaranKelas->gurus->pluck('nama_lengkap')->join(', ') }}</p>
        </div>
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Nilai Lengkap</p>
            <p class="font-medium">{{ $rekap['jumlah_lengkap'] }}/{{ $rekap['jumlah_siswa'] }} siswa</p>
        </div>
    </div>

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold text-right">Terisi</th>
                    <th class="px-5 py-3 font-semibold text-right">Rata-rata Kelas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekap['items'] as $item)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4">
                            <p class="font-medium">{{ $item['nama'] }}</p>
                            <p class="text-xs text-hitam2">{{ $item['section'] }} &middot; bobot {{ $item['bobot'] }}%</p>
                        </td>
                        <td class="px-5 py-4 text-right text-hitam2">{{ $item['terisi'] }}/{{ $rekap['jumlah_siswa'] }}</td>
                        <td class="px-5 py-4 text-right font-medium">{{ $item['rata'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-10 text-center text-hitam2">Belum ada penilaian berbobot di semester ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($rekap['items']->isNotEmpty())
        <div class="flex items-center justify-between px-1 py-4">
            <p class="font-display font-semibold text-teal">Rata-rata Hasil Akhir</p>
            <p class="font-display font-semibold text-lg">{{ $rekap['rata_akhir'] ?? '-' }}</p>
        </div>
        <p class="text-xs text-hitam2 px-1">Dihitung dari siswa yang nilainya sudah lengkap.</p>
    @endif
</x-app-layout>