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

        @include('partials.semester-tabs')
        @include('partials.rekap-nilai')
    </div>
</x-app-layout>