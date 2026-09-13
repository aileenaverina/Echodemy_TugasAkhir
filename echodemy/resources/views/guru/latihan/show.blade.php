<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.kelas.index') }}" class="hover:underline">Kelas Saya</a> &middot;
            <a href="{{ route('guru.mata-pelajaran-kelas.show', $latihan->bahanAjar->mataPelajaranKelas) }}"
                class="hover:underline">{{ $latihan->bahanAjar->mataPelajaranKelas->mataPelajaran->nama }}</a>
        </p>
        <div class="flex items-center justify-between">
            <h1 class="font-display text-2xl font-semibold">{{ $latihan->judul }}</h1>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div class="bg-putih rounded-2xl shadow-sm p-5 flex items-center gap-10">
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Terkumpul</p>
                <p class="font-medium">{{ $submissions->count() }}/{{ $totalSiswa }} Siswa</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Nilai Maksimal</p>
                <p class="font-medium">{{ $nilaiMaksimal }}</p>
            </div>
            <a href="{{ route('guru.latihan.soal', $latihan) }}"
                class="bg-cream2 text-ink text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-border">
                Lihat Soal
            </a>
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5 text-right">
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Bobot Persentase dari Nilai
                Akhir</p>
            <p class="font-display font-semibold text-lg">{{ $latihan->persen_nilai_akhir ?? 0 }}%</p>
        </div>
    </div>

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">Percobaan</th>
                    <th class="px-5 py-3 font-semibold">Pengumpulan</th>
                    <th class="px-5 py-3 font-semibold">Nilai</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $item)
                    @php
                        $nilai = $item['terakhir']->answers->sum('nilai');
                    @endphp
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $item['siswa']->nama_lengkap }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $item['percobaan'] }}/{{ $latihan->maksimal_percobaan }}
                        </td>
                        <td class="px-5 py-4 text-hitam2">{{ $item['terakhir']->created_at->format('d/m/y H.i.s') }}
                        </td>
                        <td class="px-5 py-4">{{ $nilai }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('guru.koreksi.show', [$latihan, $item['terakhir']]) }}"
                                class="inline-flex w-8 h-8 rounded-lg bg-amber/60 text-hitam items-center justify-center hover:bg-amber/70">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-hitam2">Belum ada siswa yang mengerjakan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        
    </script>
</x-app-layout>
