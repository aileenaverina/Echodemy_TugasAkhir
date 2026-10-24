<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Nilai dan Evaluasi</p>
            <h1 class="font-display text-2xl font-semibold">Nilai {{ $anak?->nama_lengkap ?? '-' }}</h1>
        </div>
    </x-slot>

    @if (!$anak)
        <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">
            Data anak tidak ditemukan.
        </div>
    @elseif ($mataPelajaranKelasList->isEmpty())
        <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">
            Belum ada mata pelajaran di kelas anak kamu.
        </div>
    @else
        @include('partials.daftar-raport')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($mataPelajaranKelasList as $mpk)
                @php
                    $isImage =
                        $mpk->mataPelajaran->file && preg_match('/\.(jpg|jpeg|png|webp)$/i', $mpk->mataPelajaran->file);
                @endphp
                <a href="{{ route('ortu.nilai.show', $mpk) }}"
                    class="block bg-putih rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="h-36 bg-cream2 bg-cover bg-center"
                        @if ($isImage) style="background-image: url('{{ \Storage::url($mpk->mataPelajaran->file) }}')" @endif>
                    </div>
                    <div class="px-4 py-3" style="background-color: {{ $mpk->mataPelajaran->warna }}">
                        <p class="font-display font-semibold text-sm"
                            style="color: #FFFFFF; text-shadow: -0.5px -0.5px 0 rgba(0,0,0,0.25), 0.5px -0.5px 0 rgba(0,0,0,0.25), -0.5px 0.5px 0 rgba(0,0,0,0.25), 0.5px 0.5px 0 rgba(0,0,0,0.25);">
                            {{ $mpk->mataPelajaran->nama }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>
