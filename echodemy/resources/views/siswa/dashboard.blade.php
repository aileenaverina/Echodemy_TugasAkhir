<x-app-layout>
    <x-slot name="header">
        <h1 class="font-display text-xl font-semibold text-teal">Hai, {{ auth()->user()->nama_lengkap }}!</h1>
    </x-slot>

    @if ($kelas)
        <div class="bg-putih rounded-2xl shadow-sm p-5 mb-8 flex items-center gap-10">
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Kelas</p>
                <p class="font-display font-semibold text-lg">{{ $kelas->nama }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Siswa</p>
                <p class="font-display font-semibold text-lg">{{ $jumlahSiswaKelas }} Siswa</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Wali Kelas</p>
                <p class="font-display font-semibold text-lg">{{ $waliKelas?->nama_lengkap ?? '-' }}</p>
            </div>
        </div>

        <h2 class="font-display text-lg font-semibold mb-3">Mata Pelajaran Saya</h2>

        @if ($mataPelajaranKelasList->isEmpty())
            <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">
                Belum ada mata pelajaran di kelasmu.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($mataPelajaranKelasList as $item)
                    @php
                        $isImage = $item->mata_pelajaran->file && preg_match('/\.(jpg|jpeg|png|webp)$/i', $item->mata_pelajaran->file);
                    @endphp
                    <a href="{{ route('siswa.mata-pelajaran-kelas.show', $item->mpk_id) }}" class="block bg-putih rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition"></a>
                        <div class="h-28 bg-cream2 bg-cover bg-center"
                            @if ($isImage) style="background-image: url('{{ \Storage::url($item->mata_pelajaran->file) }}')" @endif>
                        </div>
                        <div class="p-4">
                            <p class="font-display font-semibold">{{ $item->mata_pelajaran->nama }}</p>

                            <div class="w-full h-1.5 rounded-full bg-cream2 mt-3 overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $item->persen }}%; background-color: {{ $item->mata_pelajaran->warna }}"></div>
                            </div>

                            <p class="text-xs text-hitam2 mt-2">{{ $item->materi_selesai }} dari {{ $item->total_materi }} Materi Selesai</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    @else
        <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">
            Kamu belum terdaftar di kelas manapun.
        </div>
    @endif
</x-app-layout>